<?php

declare(strict_types=1);

namespace Cortex\Auth\Transformers\Managerarea;

use Cortex\Auth\Models\Member;
use Rinvex\Support\Traits\Escaper;
use League\Fractal\TransformerAbstract;

class MemberTransformer extends TransformerAbstract
{
    use Escaper;

    /**
     * @return array
     */
    public function transform(Member $member): array
    {
        $country = $member->country_code ? country($member->country_code) : null;
        $language = $member->language_code ? language($member->language_code) : null;

        return $this->escape([
            'id' => (string) $member->getRouteKey(),
            'DT_RowId' => 'row_'.$member->getRouteKey(),
            'is_active' => (bool) $member->is_active,
            'given_name' => (string) $member->given_name,
            'family_name' => (string) $member->family_name,
            'username' => (string) $member->username,
            'email' => (string) $member->email,
            'phone' => (string) $member->phone,
            'country_code' => (string) optional($country)->getName(),
            'country_emoji' => (string) optional($country)->getEmoji(),
            'language_code' => (string) optional($language)->getName(),
            'title' => (string) $member->title,
            'organization' => (string) $member->organization,
            'birthday' => (string) $member->birthday,
            'gender' => (string) $member->gender,
            'social' => (array) $member->social,
            'last_activity' => (string) $member->last_activity,
            'created_at' => (string) $member->created_at,
            'updated_at' => (string) $member->updated_at,
        ]);
    }
}
