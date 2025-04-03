<?php

namespace Neon\Redirect\Policies;

use Neon\Admin\Models\Admin;

class RedirectPolicy
{
	/**
	 * Perform pre-authorization checks on the model.
	 */
	public function before(Admin $user, string $ability): ?bool
	{
		if (in_array($user->email, config('neon-redirect.authorized_emails'))) {
			return true;
		}

		return false;
	}
}
