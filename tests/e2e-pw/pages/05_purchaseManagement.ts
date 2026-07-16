import { Page, expect } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export type PurchaseVendorData = {
    name: string;
    email?: string;
};

export type PurchaseProductData = {
    name: string;
    price: string;
};

export type PurchaseAgreementUpdateData = {
    reference?: string;
    quantity?: string;
    unitPrice?: string;
};

export type PurchaseQuotationData = {
    vendorName: string;
    productName: string;
    quantity: string;
    unitPrice: string;
};

export type PurchaseAgreementData = {
    vendorName: string;
    productName: string;
    quantity: string;
    unitPrice: string;
    reference: string;
};

export type PurchaseAgreementQuotationData = {
    vendorName: string;
    quantity: string;
};

export class PurchaseFlowPage {
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    async ensurePurchasesPluginInstalled() {
        const pluginPage = new PluginManagementPage(this.page);
        await pluginPage.gotoPluginManagementPage();
        await pluginPage.installPluginByName("Purchases");
    }

    async gotoPurchaseSettingsPage() {
        await this.page.goto("/admin/settings/purchase/manage-orders");
        await expect(this.page).toHaveURL(/admin\/settings\/purchase\/manage-orders/);
        await expect(this.erpLocators.purchaseAgreementSettingsToggle).toBeVisible();
    }

    async setPurchaseAgreementsEnabled(enabled: boolean) {
        await this.gotoPurchaseSettingsPage();

        const toggle = this.erpLocators.purchaseAgreementSettingsToggle;
        const tagName = await toggle.evaluate((element) => element.tagName.toLowerCase());
        const isEnabled = tagName === "input"
            ? await toggle.isChecked()
            : (await toggle.getAttribute("aria-checked")) !== "false";

        if (isEnabled !== enabled) {
            await toggle.click();
            await this.erpLocators.settingsSaveButton.click();
            await this.expectSuccessToast();
        }
    }

    async gotoVendorsPage() {
        await this.page.goto("/admin/purchase/orders/vendors");
        await expect(this.page).toHaveURL(/admin\/purchase\/orders\/vendors/);
        await expect(this.erpLocators.purchaseVendorNewCreateButton).toBeVisible();
        await expect(this.erpLocators.purchaseVendorsTable.first()).toBeVisible();
    }

    async createVendor(vendor: PurchaseVendorData) {
        await this.gotoVendorsPage();
        await this.erpLocators.purchaseVendorNewCreateButton.click();
        await expect(this.page).toHaveURL(/vendors\/create/);

        await this.erpLocators.purchaseVendorNameInput.fill(vendor.name);

        if (vendor.email) {
            await this.erpLocators.purchaseVendorEmailInput.fill(vendor.email);
        }

        await this.erpLocators.purchaseVendorSaveButton.click();
        await this.expectSuccessToast();
    }

    async createVendorExpectingValidationError() {
        await this.gotoVendorsPage();
        await this.erpLocators.purchaseVendorNewCreateButton.click();
        await expect(this.page).toHaveURL(/vendors\/create/);
        await this.erpLocators.purchaseVendorSaveButton.click();
        await this.expectValidationErrors();
    }

    async editVendor(originalName: string, updates: PurchaseVendorData) {
        await this.gotoVendorsPage();
        await this.searchList(originalName);
        // await this.openRowActions();
        // await this.clickMenuAction(/Edit/i);
        await this.erpLocators.purchaseVendorEditButton.click();

        if (updates.name) {
            await this.erpLocators.purchaseVendorNameInput.fill(updates.name);
        }

        if (updates.email) {
            await this.erpLocators.purchaseVendorEmailInput.fill(updates.email);
        }

        await this.erpLocators.purchaseVendorSaveButton.click();
        await this.expectSuccessToast();
    }

    async deleteVendor(name: string) {
        await this.gotoVendorsPage();
        await this.searchList(name);
        // await this.openRowActions();
        // await this.clickMenuAction(/Delete/i);
        await this.erpLocators.purchaseVendorDeleteButton.click();
        await this.erpLocators.purchaseConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async gotoProductsPage() {
        await this.page.goto("/admin/purchase/products/products");
        await expect(this.page).toHaveURL(/admin\/purchase\/products\/products/);
        await expect(this.erpLocators.purchaseProductNewCreateButton).toBeVisible();
        await expect(this.erpLocators.purchaseProductsTable.first()).toBeVisible();
    }

    async createProduct(product: PurchaseProductData) {
        await this.gotoProductsPage();
        await this.erpLocators.purchaseProductNewCreateButton.click();
        await expect(this.page).toHaveURL(/products\/create/);

        await this.erpLocators.purchaseProductNameInput.fill(product.name);
        await this.erpLocators.purchaseProductPriceInput.fill(product.price);

        // await this.erpLocators.purchaseProductSaveButton.click();
        await this.erpLocators.purchaseProductCreateButton.click();
        await this.expectSuccessToast();
    }

    async createProductExpectingValidationError() {
        await this.gotoProductsPage();
        await this.erpLocators.purchaseProductNewCreateButton.click();
        await expect(this.page).toHaveURL(/products\/create/);
        await this.erpLocators.purchaseProductCreateButton.click();
        await this.expectValidationErrors();
    }

    async editProduct(originalName: string, updates: PurchaseProductData) {
        await this.gotoProductsPage();
        await this.searchList(originalName);
        await this.openRowActions();
        await this.erpLocators.purchaseProductEditButton.click();
        // await this.clickMenuAction(/Edit/i);


        if (updates.name) {
            await this.erpLocators.purchaseProductNameInput.fill(updates.name);
        }

        if (updates.price) {
            await this.erpLocators.purchaseProductPriceInput.fill(updates.price);
        }

        await this.erpLocators.purchaseProductSaveButton.click();
        await this.expectSuccessToast();
    }

    async deleteProduct(name: string) {
        await this.gotoProductsPage();
        await this.searchList(name);
        await this.openRowActions();
        // await this.clickMenuAction(/Delete/i);
        await this.erpLocators.purchaseProductDeleteButton.click();
        await this.erpLocators.purchaseConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async gotoQuotationsPage() {
        await this.page.goto("/admin/purchase/orders/quotations");
        await expect(this.page).toHaveURL(/admin\/purchase\/orders\/quotations/);
        await expect(this.erpLocators.purchaseQuotationCreateButton).toBeVisible();
        await expect(this.erpLocators.purchaseQuotationsTable.first()).toBeVisible();
    }

    async createQuotation(quotation: PurchaseQuotationData) {
        await this.gotoQuotationsPage();
        await this.erpLocators.purchaseQuotationCreateButton.click();
        await expect(this.page).toHaveURL(/quotations\/create/);

        await this.selectBySearch(this.erpLocators.purchaseQuotationVendorSelect, quotation.vendorName);
        await this.erpLocators.purchaseQuotationAddProductButton.click();
        await this.selectBySearch(this.erpLocators.purchaseQuotationProductSelect.first(), quotation.productName);
        await this.erpLocators.purchaseQuotationQuantityInput.first().fill(quotation.quantity);
        await this.erpLocators.purchaseQuotationUnitPriceInput.first().fill(quotation.unitPrice);

        await this.erpLocators.purchaseQuotationSaveButton.click();
        await this.expectSuccessToast();
    }

    async editQuotationQuantity(searchKey: string, quantity: string, unitPrice?: string) {
        await this.gotoQuotationsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        // await this.clickMenuAction(/Edit/i);
        await this.erpLocators.purchaseQuotationEditButton.click();

        await this.erpLocators.purchaseQuotationQuantityInput.first().fill(quantity);

        if (unitPrice) {
            await this.erpLocators.purchaseQuotationUnitPriceInput.first().fill(unitPrice);
        }

        await this.erpLocators.purchaseQuotationSavechangesButton.click();
        await this.expectSuccessToast();
    }

    async deleteQuotation(searchKey: string) {
        await this.gotoQuotationsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        // await this.clickMenuAction(/Delete/i);
        await this.erpLocators.purchaseQuotationDeleteButton.click();
        await this.erpLocators.purchaseConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async createQuotationFromAgreement(quotation: PurchaseAgreementQuotationData) {
        await this.gotoQuotationsPage();
        await this.erpLocators.purchaseQuotationCreateButton.click();
        await expect(this.page).toHaveURL(/quotations\/create/);

        await this.selectBySearch(this.erpLocators.purchaseQuotationVendorSelect, quotation.vendorName);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.purchaseQuotationQuantityInput.first()).toBeVisible();
        await this.erpLocators.purchaseQuotationQuantityInput.first().fill(quotation.quantity);

        await this.erpLocators.purchaseQuotationSaveButton.click();
        await this.expectSuccessToast();
    }

    async confirmCurrentQuotation() {
        await expect(this.erpLocators.purchaseQuotationConfirmButton).toBeVisible();
        await this.erpLocators.purchaseQuotationConfirmButton.click();

        if (await this.erpLocators.purchaseDialogConfirmButton.isVisible().catch(() => false)) {
            await this.erpLocators.purchaseDialogConfirmButton.click();
        }

        await this.expectSuccessToast();
    }

    async gotoPurchaseOrdersPage() {
        await this.page.goto("/admin/purchase/orders/purchase-orders");
        await expect(this.page).toHaveURL(/admin\/purchase\/orders\/purchase-orders/);
        await expect(this.erpLocators.purchaseOrdersTable.first()).toBeVisible();
    }

    async expectPurchaseOrderVisible(searchKey: string) {
        await this.gotoPurchaseOrdersPage();
        await this.searchList(searchKey);
        await expect(this.page.getByText(searchKey).first()).toBeVisible();
    }

    async gotoPurchaseAgreementsPage() {
        await this.page.goto("/admin/purchase/orders/purchase-agreements");
        await expect(this.page).toHaveURL(/admin\/purchase\/orders\/purchase-agreements/);
        await expect(this.erpLocators.purchaseAgreementCreateButton).toBeVisible();
        await expect(this.erpLocators.purchaseAgreementTable.first()).toBeVisible();
    }

    async createPurchaseAgreement(agreement: PurchaseAgreementData) {
        await this.gotoPurchaseAgreementsPage();
        await this.erpLocators.purchaseAgreementCreateButton.click();
        await expect(this.page).toHaveURL(/purchase-agreements\/create/);

        await this.selectBySearch(this.erpLocators.purchaseAgreementVendorSelect, agreement.vendorName);
        await this.erpLocators.purchaseAgreementReferenceInput.fill(agreement.reference);
        await this.selectBySearch(this.erpLocators.purchaseAgreementProductSelect.first(), agreement.productName);
        await this.erpLocators.purchaseAgreementQuantityInput.first().fill(agreement.quantity);
        await this.erpLocators.purchaseAgreementUnitPriceInput.first().fill(agreement.unitPrice);

        await this.erpLocators.purchaseAgreementSaveButton.click();
        await this.expectSuccessToast();
    }

    async createPurchaseAgreementExpectingValidationError() {
        await this.gotoPurchaseAgreementsPage();
        await this.erpLocators.purchaseAgreementCreateButton.click();
        await expect(this.page).toHaveURL(/purchase-agreements\/create/);
        await this.erpLocators.purchaseAgreementSaveButton.click();
        await this.expectPurchaseAgreementValidationErrors();
    }

    async editPurchaseAgreement(searchKey: string, updates: PurchaseAgreementUpdateData) {
        await this.gotoPurchaseAgreementsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        // await this.clickMenuAction(/Edit/i);
        await this.erpLocators.purchaseAgreementEditButton.click();

        if (updates.reference) {
            await this.erpLocators.purchaseAgreementReferenceInput.fill(updates.reference);
        }

        if (updates.quantity) {
            await this.erpLocators.purchaseAgreementQuantityInput.first().fill(updates.quantity);
        }

        if (updates.unitPrice) {
            await this.erpLocators.purchaseAgreementUnitPriceInput.first().fill(updates.unitPrice);
        }

        await this.erpLocators.purchaseAgreementSaveButton.click();
        await this.expectSuccessToast();
    }

    async deletePurchaseAgreement(searchKey: string) {
        await this.gotoPurchaseAgreementsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        // await this.clickMenuAction(/Delete/i);
        await this.erpLocators.purchaseAgreementDeleteButton.click();
        await this.erpLocators.purchaseConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async confirmCurrentPurchaseAgreement() {
        await expect(this.erpLocators.purchaseAgreementConfirmButton).toBeVisible();
        await this.erpLocators.purchaseAgreementConfirmButton.click();
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.purchaseAgreementConfirmedRadio).toBeChecked();
    }

    async expectQuotationValidationErrors() {
        await expect(this.erpLocators.purchaseQuotationValidationMessage.first()).toBeVisible();
    }

    async expectPurchaseAgreementValidationErrors() {
        await expect(this.erpLocators.purchaseAgreementValidationMessage.first()).toBeVisible();
    }

    async expectValidationErrors() {
        await expect(this.erpLocators.purchaseValidationMessage.first()).toBeVisible();
    }

    async searchList(keyword: string) {
        await this.erpLocators.purchaseSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
    }

    async openRowActions() {
        await this.erpLocators.purchaseRowActionsButton.first().click();
    }

    async clickMenuAction(label: RegExp) {
        const menuItem = this.page.getByRole("menuitem", { name: label }).first();
        if (await menuItem.isVisible().catch(() => false)) {
            await menuItem.click();
            return;
        }

        const fallback = this.page
            .locator("a.fi-ac-btn-action, button.fi-ac-btn-action")
            .filter({ hasText: label })
            .first();

        await fallback.click();
    }

    async selectBySearch(trigger: ReturnType<Page["locator"]>, value: string) {
        await trigger.click();

        const option = this.erpLocators.salesSelectOption
            .filter({ hasText: new RegExp(this.escapeRegExp(value), "i") })
            .first();

        await expect(this.erpLocators.salesSelectSearchInput).toBeVisible();
        await this.erpLocators.salesSelectSearchInput.fill(value);
        await expect(option).toBeVisible();
        await option.click();
    }

    private escapeRegExp(value: string): string {
        return value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    }

    private async expectSuccessToast() {
        await expect(this.erpLocators.purchaseSuccessToast).toBeVisible();
    }
}
