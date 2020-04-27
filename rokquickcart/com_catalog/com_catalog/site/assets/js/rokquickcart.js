((function(){
    Array.max = function(arr){
        return Math.max.apply(Math, arr);
    };

    var RokQuickCart = {
        init: function(){
            RokQuickCart.blocks = document.getElements('.cart_same_height .cart_product_content');

            RokQuickCart.update();
            RokQuickCart.setCart();
        },

        update: function(){
            if (!RokQuickCart.blocks.length){
                window.removeEvent('resize', RokQuickCart.update);
                return;
            }

            var blocks = RokQuickCart.blocks, winSize = window.getSize().x;
            var rows = blocks[0].getParent('[data-rqc-columns]').getProperty('data-rqc-columns'), b = [], h = [];

            rows = parseInt(rows, 10);
            if (winSize >= 768 && winSize <= 959) rows = 2;
            if (winSize <= 767) rows = 1;

            blocks.removeProperty('style');
            for (var i = 0, l = blocks.length; i < l; i = i + rows){
                b = []; h = [];
                for(var j = 0, k = rows; j < k; j++){
                    if (blocks[i + j]){
                        b.push(blocks[i + j]);
                        h.push(blocks[i + j].getSize().y);
                    }
                };

                $$(b).setStyle('height', Array.max(h));

            };

            /*RokQuickCart.blocks.forEach(function(block, i){
                block.removeProperty('style');
                currentHeight = block.getSize().y;
                if (currentHeight > height) height = currentHeight;
            });*/

            //RokQuickCart.blocks.setStyle('height', height);
        },

        setCart: function(){
            if (typeof simpleCart == 'undefined') return;
            simpleCart({
                cartColumns: [
                    { attr: "image", label: false, view: function(item, column){ return "<a href='" + item.get(column.attr) + "' data-rokbox><img src='" + item.get(column.attr) + "'/></a>"; }},
                    { attr: "name" , label: "Producto", view: function(item, column){
                            var options = item.options(), option, badges = [], cleanKey, cleanValue;
                            for (option in options){
                                if (option == "image") continue;
                                cleanKey = option.replace(/-/g, " ").capitalize();
                                cleanValue = options[option].replace(/_/g, " ");

                                badges.push('<span class="cart_badge">'+cleanValue+'</span>');
                            }
                            if (!badges.length) return item.get(column.attr) || "";
                            else return (item.get(column.attr) || "") + '<div class="cart_badges">'+ badges.join(" ") +'</div>';
                        }
                    },
                    { attr: "quantity" , label: "Cantidad", view: "input" },
                    { view: "remove" , text: "Quitar" , label: false },
                    { attr: "price" , label: "Precio", view: "currency" },
                    { attr: "total" , label: "Total", view: "currency" }
                ]
            });
        }
    };

    window.addEvent('domready', RokQuickCart.init);
    window.addEvent('load', RokQuickCart.update);
    window.addEvent('resize', RokQuickCart.update);

})());
