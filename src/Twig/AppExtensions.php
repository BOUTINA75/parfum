<?php 

namespace App\Twig;

use App\Classe\Cart;
use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;


class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;
    private $cart;
    public function __construct(CategoryRepository $categoryRepository, Cart $cart)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cart = $cart;
    }

    // (je créer une nouvelle extention et je déclare un nouveau filtre qui est price, j'ai besoin de lui dire comment les chose vont se compodrdter quand je vais utiliser ce filtre price ) Je créer mon filtre que je vais utiliser directement dans mes templates twig,
    // sa me permet de dire voilà le nom du filtre que je te passe, voilà l'objet concerné par la classe et en fin voilà le nom de la fonction que tu dois utiliser pour faire le traitement de se filtre
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice'])
        ];
    }

    // Je créer une nouvelle fonction que je vais nommer formatPrice
    public function formatPrice($number)
    {
        return number_format($number,'2', ','). '€';
    }

    // Je créer une nouvelle extention getGlobals qui va me permet dans le fichier extension de twig de créer des variables globales que je vais pouvoir utiliser partout dans notre environnement (je vais return le nom de la variable que je souhaite utiliser dans mon tableau) j'implements la GlobalInteface dans cette interface on m'oblige à utiliser cette fonction getGlobal
    public function getGlobals(): array
    {
        return [
            'allCategories' => $this->categoryRepository->findAll(),
            'fullCartQuantity' => $this->cart->fullQuantity()
        ];
    }
}
