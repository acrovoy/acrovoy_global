<?php

namespace App\Policies;

use App\Models\Buyer;
use App\Models\User;

class BuyerCompanyPolicy extends BasePolicy
{
    /**
     * View buyer company profile.
     */
    public function viewAny(User $user): bool
    {
        return $this->isBuyer()
            && $this->buyer() !== null;
    }

    /**
     * View specific buyer company.
     */
    public function view(User $user, Buyer $buyer): bool
    {
        return $this->ownsBuyer($buyer);
    }

    /**
     * Update company profile.
     *
     * Personal buyer:
     *      allowed.
     *
     * Company buyer:
     *      owner / administrator.
     */
    public function update(User $user, Buyer $buyer): bool
    {
        if (!$this->ownsBuyer($buyer)) {
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
    public function updateProfile(User $user, Buyer $buyer): bool
    {
        return $this->update($user, $buyer);
    }

    /**
     * Open profile drawer.
     */
    public function drawer(User $user, Buyer $buyer): bool
    {
        return $this->ownsBuyer($buyer);
    }

    /**
     * Upload/update company logo.
     */
    public function updateLogo(User $user, Buyer $buyer): bool
    {
        return $this->canManage($buyer);
    }

    /**
     * Upload certificate.
     */
    public function uploadCertificate(User $user, Buyer $buyer): bool
    {
        return $this->canManage($buyer);
    }

    /**
     * Delete certificate.
     */
    public function deleteCertificate(User $user, Buyer $buyer): bool
    {
        return $this->canManage($buyer);
    }

    /**
     * Upload factory photos.
     */
    public function uploadFactoryPhotos(User $user, Buyer $buyer): bool
    {
        return $this->canManage($buyer);
    }

    /**
     * Delete factory photo.
     */
    public function deleteFactoryPhoto(User $user, Buyer $buyer): bool
    {
        return $this->canManage($buyer);
    }

    /**
     * Manage company members.
     */
    public function manageMembers(User $user, Buyer $buyer): bool
    {
        if (!$this->ownsBuyer($buyer)) {
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
    protected function canManage(Buyer $buyer): bool
    {
        if (!$this->ownsBuyer($buyer)) {
            return false;
        }

        if ($this->isPersonal()) {
            return true;
        }

        return $this->isCompanyManager();
    }
}