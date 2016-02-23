<?php

namespace RetailExpress\SkyLink\Outlets;

use RetailExpress\SkyLink\Abn;
use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Company;
use RetailExpress\SkyLink\SalesChannelId;

class V2OutletRepository implements OutletRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function all(SalesChannelId $salesChannelId)
    {
        // $rawResponse = $this->api->call('OutletsGetByChannel', [
        //     'ChannelId' => $salesChannelId->toInt(),
        // ]);

        $outletA = Outlet::existing(
            new OutletId(123),
            'Outlet A',
            Address::newInstance(
                ['Unit 5', 'Level 5', '192 Ann St'],
                'Brisbane',
                '4000',
                'Queensland',
                'Australia',
                [
                    'phone' => '(02) 1111 1111',
                    'fax' => '(02) 2222 2222',
                ]
            ),
            new Company(
                'Ben Co Ltd',
                new Abn(123456789, 'ABN')
            )
        );

        $outletB = Outlet::existing(
            New OutletId(124),
            'Outlet B',
            Address::newInstance([], null, null, null, 'Australia')
        );
    }
}
