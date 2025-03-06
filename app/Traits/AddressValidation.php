<?php

namespace App\Traits;

trait AddressValidation
{
    // Task: Simple address validation logic
    protected function validateAddress(array $address): bool
    {
        // Basic validation rules for the task
        $required = ['street', 'city', 'state', 'zip'];
        
        foreach ($required as $field) {
            if (empty($address[$field])) {
                return false;
            }
        }
        
        // ZIP code format check (basic US format)
        if (!preg_match('/^\d{5}(-\d{4})?$/', $address['zip'])) {
            return false;
        }
        
        return true;
    }
} 