class DiscardedCard
{
    static createCard(card, draggable) {
        $('#discarded_pool').append(
            `<div id="card_discarded_pool" data-card-id="${card}" class="card table ${CardHelper.getValueFromId(card)} ${draggable ? 'draggable' : ''}"></div>`
        );
    }

    static resetCard() {
        const card = $("#card_discarded_pool");
        card.data({
            'originalLeft': card.css('left'),
            'origionalTop': card.css('top')
        });

        if (card.hasClass('draggable')) {
            card.draggable({
                drag: function (event, ui) {
                    dragCheck = true;

                    $('.card_hand_dropper').css('display', 'block');

                },
                start: function (event, ui) {

                    $(this).css('z-index', ++z);
                    drag_source = $(this).parent().attr('id');
                },
                stop: function (event, ui) {
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
}