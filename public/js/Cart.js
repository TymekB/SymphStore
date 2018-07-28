class Cart
{
    constructor($cartId)
    {
        this.cart = $($cartId);
    }

    getProducts() {
        let tr = this.cart.find('tr');
        let products = [];

        tr.each(function() {
            let id = parseInt($(this).find('.product-id').html());
            let price = parseFloat($(this).find('.price').html());
            let quantity = parseInt($(this).find('.quantity').val());

            if(!id) {
                return;
            }

            products.push({id: id, price: price, quantity: quantity});
        });

        return products;
    }

    getTotal() {
        let products = this.getProducts();
        let total = 0;

        for(let i = 0; i < products.length; i++) {
            total += products[i].price * products[i].quantity;
        }

        total = Math.floor(total * 100) / 100;

        return total;
    }
}