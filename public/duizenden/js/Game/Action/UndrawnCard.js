class UndrawnCard
{
    static updateColor(color_id) {
        const card = $("#card-undrawn-pool");
        const color = CardHelper.createColorFromColorId(color_id);

        if (!card.hasClass(color)) {
            card.removeClass().addClass(`card table ${color}`);
        }
    }

    static resetCard() {
        const card = $("#card-undrawn-pool");
        card.data({
            'originalLeft': card.css('left'),
            'origionalTop': card.css('top')
        });

        card.draggable({
            // drag: function (event, ui) {
            //
            // },
            start: function (event, ui) {
                dragCheck = true;

                $(this).css('z-index', 9999);
                drag_source = $(this).parent().attr('id');
                $('.card-hand-dropper').show();
            },
            stop: function (event, ui) {
                $(this).css('z-index', 1);
                $(this).css({
                    'left': $(this).data('originalLeft'),
                    'top': $(this).data('origionalTop')
                });
                dragCheck = false;
                $('.card-hand-dropper').hide();
            }
        });
    }

    static disableDraggable() {
        $("#card-undrawn-pool").draggable('disable');
    }

    static enableDraggable() {
        $("#card-undrawn-pool").draggable('enable');
    }
}