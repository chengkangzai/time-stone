<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Donate
 *
 * @property int $id
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Payment|null $payments
 * @method static \Illuminate\Database\Eloquent\Builder|Donate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Donate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Donate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Donate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Donate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Donate wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Donate whereUpdatedAt($value)
 */
	class Donate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MicrosoftOAuth
 *
 * @property int $id
 * @property string $accessToken
 * @property string $refreshToken
 * @property string $tokenExpires
 * @property string $userName
 * @property string $userEmail
 * @property string $userTimeZone
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth query()
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereTokenExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereUserEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicrosoftOAuth whereUserTimeZone($value)
 */
	class MicrosoftOAuth extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $user_id
 * @property int $donate_id
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Donate $donate
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDonateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUserId($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ScheduleConfig
 *
 * @property int $id
 * @property string $intake_code
 * @property string $grouping
 * @property string|null $except
 * @property string|null $emails
 * @property int $is_subscribed
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig subscribed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig whereEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig whereExcept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig whereGrouping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig whereIntakeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig whereIsSubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleConfig whereUserId($value)
 */
	class ScheduleConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property-read \App\Models\MicrosoftOAuth|null $msOauth
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\ScheduleConfig|null $scheduleConfig
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Cashier\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

