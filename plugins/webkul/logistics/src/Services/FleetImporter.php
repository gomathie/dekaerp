<?php

namespace Webkul\Logistics\Services;

use Illuminate\Support\Facades\DB;
use Webkul\Employee\Models\Employee;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Vehicle;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Maintenance\Models\Equipment;
use Webkul\PluginManager\Package;

class FleetImporter
{
    /**
     * @param  array<int>  $employeeIds
     * @return array{created: int, skipped: int}
     */
    public function driversFromEmployees(array $employeeIds): array
    {
        $created = 0;
        $skipped = 0;

        Employee::query()
            ->whereKey($employeeIds)
            ->orderBy('id')
            ->get()
            ->each(function (Employee $employee) use (&$created, &$skipped): void {
                if (! $employee->company_id) {
                    $skipped++;

                    return;
                }

                LogisticsAccess::ensureEnabled((int) $employee->company_id);

                DB::transaction(function () use ($employee, &$created, &$skipped): void {
                    if (Driver::query()->where('employee_id', $employee->id)->exists()) {
                        $skipped++;

                        return;
                    }

                    Driver::query()->create([
                        'name'        => $employee->name,
                        'phone'       => $employee->work_phone ?? $employee->mobile_phone,
                        'employee_id' => $employee->id,
                        'company_id'  => $employee->company_id,
                        'is_active'   => true,
                    ]);

                    $created++;
                });
            });

        return compact('created', 'skipped');
    }

    /**
     * @param  array<int>  $equipmentIds
     * @return array{created: int, skipped: int}
     */
    public function vehiclesFromMaintenanceEquipment(array $equipmentIds): array
    {
        if (! Package::isPluginInstalled('maintenance')) {
            return ['created' => 0, 'skipped' => count($equipmentIds)];
        }

        $created = 0;
        $skipped = 0;

        Equipment::query()
            ->whereKey($equipmentIds)
            ->orderBy('id')
            ->get()
            ->each(function (Equipment $equipment) use (&$created, &$skipped): void {
                if (! $equipment->company_id) {
                    $skipped++;

                    return;
                }

                LogisticsAccess::ensureEnabled((int) $equipment->company_id);

                DB::transaction(function () use ($equipment, &$created, &$skipped): void {
                    $registration = $this->registrationFromEquipment($equipment);

                    if (
                        Vehicle::query()->where('equipment_id', $equipment->id)->exists()
                        || Vehicle::query()
                            ->where('company_id', $equipment->company_id)
                            ->where('registration_no', $registration)
                            ->exists()
                    ) {
                        $skipped++;

                        return;
                    }

                    Vehicle::query()->create([
                        'registration_no' => $registration,
                        'name'            => $equipment->name,
                        'equipment_id'    => $equipment->id,
                        'company_id'      => $equipment->company_id,
                        'is_active'       => true,
                    ]);

                    $created++;
                });
            });

        return compact('created', 'skipped');
    }

    protected function registrationFromEquipment(Equipment $equipment): string
    {
        return filled($equipment->serial_no)
            ? $equipment->serial_no
            : ($equipment->partner_ref ?: 'EQ-'.$equipment->id);
    }
}
