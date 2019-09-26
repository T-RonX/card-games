class ResetUndrawnCard
{
    static resetCard() {
        const card = $("#card_undrawn_pool");
        card.data({
            'originalLeft': card.css('left'),
            'origionalTop': card.css('top')
        });

        card.draggable({
            drag: function (event, ui) {
                dragCheck = true;

                $('.card_hand_dropper').css('display', 'block');

            },
            start: function (event, ui) {

                $(this).css('z-index', ++z);
                drag_source = $(this).parent().attr('id');
            },
            stop: function( event, ui ) {
                $(this).css('z-index', z - 1);
                $(this).css({
                        'left': $(this).data('originalLeft'),
                        'top': $(this).data('origionalTop')
                    });
                dragCheck = false;

                $('.card_hand_dropper').css('display', 'none');
            }
        });
    }
}