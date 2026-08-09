<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierCompanyPolicy extends BasePolicy
{
    /**
     * View supplier company profile.
     */
    public function viewAny(User $user): bool
    {
        return $this->isSupplier()
            && $this->supplier() !== null;
    }

    /**
     * View specific supplier company.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        return $this->ownsSupplier($supplier);
    }

    /**
     * Update company profile.
     *
     * Personal supplier:
     *      allowed.
     *
     * Company supplier:
     *      owner / administrator.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        if (!$this->ownsSupplier($supplier)) {
            return false;
        }

        if ($this->isPersonal()) {
            return true;
        }

        return $this->isCompanyManager();
    }

    /**
     * Update company overview/general/etc.
     */
    public function updateProfile(User $user, Supplier $supplier): bool
    {
        return $this->update($user, $supplier);
    }

    /**
     * Open profile drawer.
     */
    public function drawer(User $user, Supplier $supplier): bool
    {
        return $this->ownsSupplier($supplier);
    }

    /**
     * Upload/update company logo.
     */
    public function updateLogo(User $user, Supplier $supplier): bool
    {
        return $this->canManage($supplier);
    }

    /**
     * Upload certificate.
     */
    public function uploadCertificate(User $user, Supplier $supplier): bool
    {
        return $this->canManage($supplier);
    }

    /**
     * Delete certificate.
     */
    public function deleteCertificate(User $user, Supplier $supplier): bool
    {
        return $this->canManage($supplier);
    }

    /**
     * Upload factory photos.
     */
    public function uploadFactoryPhotos(User $user, Supplier $supplier): bool
    {
        return $this->canManage($supplier);
    }

    /**
     * Delete factory photo.
     */
    public function deleteFactoryPhoto(User $user, Supplier $supplier): bool
    {
        return $this->canManage($supplier);
    }

    /**
     * Manage company members.
     */
    public function manageMembers(User $user, Supplier $supplier): bool
    {
        if (!$this->ownsSupplier($supplier)) {
            return false;
        }

        if ($this->isPersonal()) {
            return false;
        }

        return $this->isCompanyManager();
    }

    /**
     * Common permission for company modifications.
     */
    protected function canManage(Supplier $supplier): bool
    {
        if (!$this->ownsSupplier($supplier)) {
            return false;
        }

        if ($this->isPersonal()) {
            return true;
        }

        return $this->isCompanyManager();
    }
}