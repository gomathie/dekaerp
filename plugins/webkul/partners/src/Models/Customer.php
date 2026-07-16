<?php

namespace Webkul\Partner\Models;

/**
 * Customer-facing partner used as the authentication model for the customer
 * panel/guard. Relocated here from the (removed) website plugin.
 */
class Customer extends Partner
{
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'password',
            'is_active',
        ]);

        $this->mergeCasts([
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ]);

        parent::__construct($attributes);
    }
}
