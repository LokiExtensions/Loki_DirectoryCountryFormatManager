<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\FixtureTrait;

use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\AuthorizationException;

trait PostFormAction
{
    private function postFormAction(string $action, array $item): void
    {
        $handle = 'directory_country_format_manager_index_form';
        $payload = [
            'targets' => [],
            'handles' => ['default', $handle],
            'pageHandles' => [],
            'updates' => [
                [
                    'blockName' => 'directory_country_format_manager.form',
                    'update' => [
                        'actions' => [$action],
                        'item' => $item,
                    ],
                ],
            ],
            'request' => [],
        ];

        $payload['signature'] = $this->_objectManager->get(AjaxSignature::class)->sign([
            'handles' => $payload['handles'],
            'pageHandles' => $payload['pageHandles'],
            'request' => $payload['request'],
        ]);

        $headers = new Headers();
        $headers->addHeader(GenericHeader::fromString('X-Alpine-Request: 1'));

        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $request->setHeaders($headers);
        $request->setMethod(HttpRequest::METHOD_POST);
        $request->setContent((string)json_encode($payload));

        try {
            $this->dispatch('backend/loki_components/index/html');
        } catch (AuthorizationException) {
        }
    }

    private function deny(string $resource): void
    {
        $acl = $this->_objectManager->get(AclBuilder::class)->getAcl();
        $acl->deny(null, $resource);
    }
}
