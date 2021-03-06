<?php

namespace Lit\Config\Crud;

use App\Models\User;
use Ignite\Crud\Config\CrudConfig;
use Ignite\Crud\CrudIndex;
use Ignite\Crud\CrudShow;
use Lit\Config\Charts\UserOrderAmountAreaChartConfig;
use Lit\Http\Controllers\Crud\CustomerController;

class CustomerConfig extends CrudConfig
{
    /**
     * Model class.
     *
     * @var string
     */
    public $model = User::class;

    /**
     * Controller class.
     *
     * @var string
     */
    public $controller = CustomerController::class;

    /**
     * Model singular and plural name.
     *
     * @param User|null user
     * @return array
     */
    public function names(User $user = null)
    {
        return [
            'singular' => $user->name ?? 'Customer',
            'plural'   => 'Customers',
        ];
    }

    /**
     * Get crud route prefix.
     *
     * @return string
     */
    public function routePrefix()
    {
        return 'customers';
    }

    /**
     * Build index page.
     *
     * @param  \Ignite\Crud\CrudIndex $page
     * @return void
     */
    public function index(CrudIndex $page)
    {
        $page->table(function ($table) {
            $table->col('Name')->value('{name}')->sortBy('name');
            $table->money('paid_amount', 'EUR', 'de_DE')->label('Paid Amount');
        })
            ->query(function ($query) {
                $query->withPaidAmount();
            })
            ->search('name', 'email')
            ->sortBy([
                'id.desc' => __lit('lit.sort_new_to_old'),
                'id.asc'  => __lit('lit.sort_old_to_new'),
            ]);
    }

    /**
     * Setup show page.
     *
     * @param  \Ignite\Crud\CrudShow $page
     * @return void
     */
    public function show(CrudShow $page)
    {
        $page->group(function ($page) {
            $page->card(function ($form) {
                $form->input('name')->width(1 / 2);
                $form->input('email')->width(1 / 2);
            });

            $page->card(function ($form) {
                $form->relation('orders');
            });
        })->width(8);

        $page->group(function ($page) {
            $page->chart(UserOrderAmountAreaChartConfig::class)->height('120px');
        })->width(4);
    }
}
