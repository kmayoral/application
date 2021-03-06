<?php
/**
 * Shopping Cart controller
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Controller_Cart extends Controller
{
	/**
	 * Adds an item to the user's shopping cart
	 *
	 * @return null
	 */
	public function action_add()
	{
		$product_id = arr::get($_GET, 'id');
		$quantity = arr::get($_GET, 'quantity', 1);

		$product = new Model_Vendo_Product($product_id);

		if ($product->id)
		{
			Auth::instance()->get_user()->cart()->add_product(
				$product,
				$quantity
			);
		}

		Request::current()->redirect('cart/index');
	}

	/**
	 * Lets the user view their shopping cart
	 *
	 * @return null
	 */
	public function action_index()
	{
		$this->view = new View_Cart_Index;
		$this->view->bind('cart', $cart);

		$cart = Auth::instance()->get_user()->cart();
	}

	/**
	 * Updates the shopping cart
	 *
	 * @return null
	 */
	public function action_update()
	{
		switch (current(arr::get($_POST, 'submit', 'Delete Selected / Update Quantities')))
		{
			case 'Empty Cart':
				Auth::instance()->get_user()->cart(new Model_Order);
				break;
			case 'Delete Selected / Update Quantities':
			default:
				foreach (
					arr::get(
						$_POST, 'new_quantity', array()
					) as $product_id => $quantity
				)
				{
					$product = new Model_Vendo_Product($product_id);

					// If it's been marked for deletion, do that instead
					if (isset($_POST['delete'][$product_id]))
					{
						Auth::instance()->get_user()->cart()->modify_quantity(
							$product,
							0
						);
						continue;
					}

					Auth::instance()->get_user()->cart()->modify_quantity(
						$product,
						$quantity
					);
				}
				break;
		}

		Request::current()->redirect('cart/index');
	}
}