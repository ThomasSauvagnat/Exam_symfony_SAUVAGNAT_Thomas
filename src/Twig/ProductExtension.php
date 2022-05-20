<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ProductExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_number_false_products_by_user', [$this, 'GetNumberFalseProductsByUser']),
        ];
    }

    public function GetNumberFalseProductsByUser(User $user): int
    {
        $userProducts = $user->getProducts();
        $userFalseProducts = [];
        foreach ($userProducts as $userProduct)
         if($userProduct->isIsActive() === false) {
             array_push($userFalseProducts, $userProduct);
         }
        $falseProductNumber = count($userFalseProducts);
        if($falseProductNumber === 0) {
            return 0;
        } else {
            return $falseProductNumber;
        }
    }
}
