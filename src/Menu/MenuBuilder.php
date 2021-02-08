<?php
namespace App\Menu;

use App\Helpers\DateInterval;
use Knp\Menu\ItemInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{
    private $factory;
    private $security;

    /**
     * Add any other dependency you need...
     */
    public function __construct(FactoryInterface $factory, Security $security)
    {
        $this->factory = $factory;
        $this->security = $security;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $attributes = [];
        $options = array_merge([
            'dropdown' => false, 
            'attributes' => [
                'class' => 'nav-item'
            ], 
            'linkAttributes' => [
                'class' => 'nav-link'
            ]
        ], $attributes);

        $options2 = array_merge([
            'dropdown' => true, 
            'attributes' => [
                'class' => 'dropdown-item'
            ], 
            'linkAttributes' => [
                'class' => 'nav-link'
            ]
        ], $attributes);
        
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav navbar-nav mr-auto');
        if (!$this->security->getUser()) {
            $menu->addChild(
                'Home', 
                array_merge($options, ['route' => 'home'])
            );
        }

        if ($this->security->isGranted('ROLE_ACCOUNTANT')) {
            $menu->addChild(
                'Purchases', 
                array_merge($options, ['route' => 'purchase'])
            );
        }
        if ($this->security->isGranted('ROLE_ACCOUNTANT')) {
            $menu->addChild(
                'Objects', 
                array_merge($options, ['route' => 'objects'])
            );
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $dropdown = $menu->addChild(
                'Transport',
                [
                'attributes' => [
                    'class' => 'nav-item dropdown'
                ],
                'labelAttributes' => [
                    'class' => 'nav-link dropdown-toggle',
                    'data-toggle' => 'dropdown',
                ]
            ]
            );
            $dropdown->setChildrenAttribute('class', 'dropdown-menu');
        
            $dropdown->addChild(
                'List',
                array_merge($options2, ['route' => 'transport_list'])
            );
            $dropdown->addChild(
                'Fuel',
                array_merge($options2, ['route' => 'transport_fuel'])
            );
            $dropdown->addChild(
                'Month usage',
                array_merge($options2, ['route' => 'transport_usage_list'])
            );
            $dropdown->addChild(
                'Report',
                array_merge($options2, ['route' => 'transport_usage_report'])
            );
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $menu->addChild(
                'Administration', 
                array_merge($options, ['route' => 'easyadmin'])
            );
        }

        if ($this->security->isGranted('ROLE_ACCOUNTANT')) {
            $statistic = $menu->addChild(
                'Statistic',
                [
                    'attributes' => [
                        'class' => 'nav-item dropdown'
                    ],
                    'labelAttributes' => [
                        'class' => 'nav-link dropdown-toggle',
                        'data-toggle' => 'dropdown',
                    ]
                ]
            );
            $statistic->setChildrenAttribute('class', 'dropdown-menu');
        
            $statistic->addChild(
                'Statistic',
                array_merge($options2, ['route' => 'statistic'])
            );
            $statistic->addChild(
                'Overview',
                array_merge($options2, ['route' => 'overview'])
            );
            $statistic->addChild(
                'Time card',
                array_merge($options2, ['route' => 'time_card_summary'])
            );
            $statistic->addChild(
                'Company details',
                array_merge($options2, ['route' => 'company_details'])
            );
        }

        return $menu;
    }

    public function createUserMenu(array $options): ItemInterface
    {
        $attributes = [];
        $options = array_merge([
            'dropdown' => false, 
            'attributes' => [
                'class' => 'nav-item'
            ], 
            'linkAttributes' => [
                'class' => 'nav-link'
            ]
        ], $attributes);

        $options2 = array_merge([
            'dropdown' => true, 
            'attributes' => [
                'class' => 'dropdown-item'
            ], 
            'linkAttributes' => [
                'class' => 'nav-link'
            ]
        ], $attributes);
        
        $menu = $this->factory->createItem('user');
        $menu->setChildrenAttribute('class', 'nav navbar-nav mr-auto');

        if ($this->security->getUser()) {
            $userName = $this->security->getUser();
        } else {
            $userName = 'Login';
        }

        $dropdown = $menu->addChild(
            $userName,
            [
            'attributes' => [
                'class' => 'nav-item dropdown'
            ],
            'labelAttributes' => [
                'class' => 'nav-link dropdown-toggle',
                'data-toggle' => 'dropdown',
            ]
        ]
        );
        $dropdown->setChildrenAttribute('class', 'dropdown-menu');

        $session = new DateInterval();
        $time = $session->getBegin()->format('Y')
                .' '.$session->getMonthWords().' mėn. ';

        if (!$this->security->getUser()) {
            $dropdown->addChild(
                'Login', 
                array_merge($options, ['route' => 'app_login'])
            );
        } else {
            $dropdown->addChild(
                $time, 
                array_merge($options, ['route' => 'set_date_interval'])
            );
            $dropdown->addChild(
                'Logout', 
                array_merge($options, ['route' => 'app_logout'])
            );
        }

        return $menu;
    }
}