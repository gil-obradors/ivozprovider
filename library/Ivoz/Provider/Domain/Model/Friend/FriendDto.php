<?php

namespace Ivoz\Provider\Domain\Model\Friend;

use Ivoz\Api\Core\Annotation\AttributeDefinition;
use Ivoz\Kam\Domain\Model\UsersLocation\RegistrationStatus;

class FriendDto extends FriendDtoAbstract
{
    const CONTEXT_STATUS = 'status';

    /**
     * @var RegistrationStatus[]
     * @AttributeDefinition(
     *     type="array",
     *     class="Ivoz\Kam\Domain\Model\UsersLocation\RegistrationStatus",
     *     description="Registration status"
     * )
     */
    protected $status = [];

    /**
     * @var string
     * @AttributeDefinition(
     *     type="string",
     *     description="Registration domain"
     * )
     */
    protected $domainName;

    public function addStatus(RegistrationStatus $status)
    {
        $this->status[] = $status;

        return $this;
    }

    public function setDomainName(string $name)
    {
        $this->domainName = $name;
    }


    public function toArray($hideSensitiveData = false)
    {
        $response = parent::toArray($hideSensitiveData);
        $response['domainName'] = $this->domainName;
        $response['status'] = $this->status;

        return $response;
    }

    public function normalize(string $context, string $role = '')
    {
        $response = parent::normalize(...func_get_args());

        if (!isset($response['status'])) {
            return $response;
        }

        /**
         * @var int $key
         * @var RegistrationStatus $status
         */
        foreach ($response['status'] as $key => $status) {
            $response['status'][$key] = $status->toArray();
        }

        return $response;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public static function getPropertyMap(string $context = '', string $role = null)
    {
        if ($context === self::CONTEXT_STATUS) {
            $baseAttributes = [
                'id' => 'id',
                'name' => 'name',
                'domainName' => 'domainName',
                'status' => [
                    'contact',
                    'expires',
                    'userAgent'
                ]
            ];

            if ($role === 'ROLE_BRAND_ADMIN') {
                $baseAttributes['companyId'] = 'company';
            }

            return $baseAttributes;
        }

        if ($context === self::CONTEXT_COLLECTION) {
            return [
                'id' => 'id',
                'name' => 'name'
            ];
        }

        $response = parent::getPropertyMap(...func_get_args());

        if ($role === 'ROLE_BRAND_ADMIN') {
            return self::filterFieldsForBrandAdmin($response);
        }

        if ($role === 'ROLE_COMPANY_ADMIN') {
            return self::filterFieldsForCompanyAdmin($response);
        }

        return $response;
    }

    private static function filterFieldsForBrandAdmin(array $response): array
    {
        $allowedFields = [
            'name',
            'description',
            'transport',
            'ip',
            'port',
            'authNeeded',
            'password',
            'priority',
            'allow',
            'fromDomain',
            'directConnectivity',
            'ddiIn',
            't38Passthrough',
            'id',
            'companyId',
            'transformationRuleSetId',
            'callAclId',
            'outgoingDdiId',
            'languageId',
            'interCompanyId'
        ];

        $response = array_filter(
            $response,
            function ($key) use ($allowedFields) {
                return in_array($key, $allowedFields, true);
            },
            ARRAY_FILTER_USE_KEY
        );

        return $response;
    }

    private static function filterFieldsForCompanyAdmin(array $response): array
    {
        $allowedFields = [
            'name',
            'description',
            'transport',
            'ip',
            'port',
            'authNeeded',
            'password',
            'priority',
            'allow',
            'fromDomain',
            'directConnectivity',
            'ddiIn',
            't38Passthrough',
            'id',
            'companyId',
            'transformationRuleSetId',
            'callAclId',
            'outgoingDdiId',
            'languageId',
        ];

        $response = array_filter(
            $response,
            function ($key) use ($allowedFields) {
                return in_array($key, $allowedFields, true);
            },
            ARRAY_FILTER_USE_KEY
        );

        return $response;
    }
}
