<?php

namespace App\Support;

class Support
{
    protected array $links;

    public function __construct()
    {
        $this->links = [
            'reach' => [
                'title' => __('Support with reach'),
                'items' => [
                    [
                        'label' => 'GitHub Follow',
                        'url' => 'https://github.com/Muetze42',
                    ],
                    [
                        'label' => 'LinkedIn Follow/Connect',
                        'url' => 'https://github.com/Muetze42',
                    ]
                ],
            ],
            'material' => [
                'title' => __('Material support'),
                'items' => [
                    [
                        'label' => 'PayPal Donation',
                        'url' => 'https://www.paypal.com/donate/?hosted_button_id=PFS2DG3CQYSFC',
                    ],
                    [
                        'label' => 'PayPal.me',
                        'url' => 'https://paypal.me/HuthNorman',
                    ],
                    [
                        'label' => 'Ko-Fi',
                        'url' => 'https://ko-fi.com/normanhuth',
                    ],
                    [
                        'label' => 'GitHub Sponsor',
                        'url' => 'https://github.com/sponsors/Muetze42',
                    ]
                ],
            ],
            'tinkerwell' => null,
            'project' => [
                'title' => __('Links for this project'),
                'items' => [
                    [
                        'label' => __('Source code on GitHub'),
                        'url' => 'https://github.com/Muetze42/hellofresh-database',
                    ]
                ]
            ],
            'advertising' => [
                'title' => __('Advertising links'),
                'items' => [
                    [
                        'label' => 'UptimeRobot - The world\'s most leading uptime monitoring service',
                        'url' => 'https://uptimerobot.com/?rid=0db9c0c413f465',
                    ],
                    [
                        'label' => 'All-Inkl.com - Webhosting',
                        'url' => 'https://all-inkl.com/PA77D721D085F2D',
                    ],
                    [
                        'label' => 'Shoop Cashback',
                        'url' => 'https://www.shoop.de/invite/lqur4UDSln/',
                    ],
                    [
                        'label' => 'iGraal Cashback',
                        'url' => 'https://de.igraal.com/einladung?werber=AG_5a1ab979a2ff2',
                    ]
                ],
            ],
        ];
    }

    /**
     * Get links as a plain array.
     */
    public function toArray(): array
    {
        return $this->links;
    }

    /**
     * Get links converted to a JSON string.
     */
    public function toJson($options = 0): false|string
    {
        return json_encode($this->links, $options);
    }
}
