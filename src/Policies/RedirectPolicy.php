<?php

namespace Neon\Redirect\Policies;

use Nitrogen\CMS\Models\Admin;

class RedirectPolicy
{
  /**
   * Perform pre-authorization checks on the model.
   */
  public function before(Admin $user, string $ability): ?bool
  {
	//TODO: Add emails to the array to allow access to the redirect resource
    if (in_array($user->email, ['...', '...', '...'])) {
      return true;
    }

    return null;
  }
}