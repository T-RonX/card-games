class DiscardedCard
{
    static createCard(card_id, draggable) {
        $('#discarded_pool').append(
            `<div id="card_discarded_pool" data-card-id="${card_id}" class="card table ${CardHelper.getValueFromId(card_id)} ${draggable ? 'draggable' : ''}"></div>`
        );
    }

    static removeCard() {
        $('#card_discarded_pool').remove();
    }

    static resetCard(card_id, draggable) {
        let card = $("#card_discarded_pool");

        if (!card.length) {
           this.createCard(card_id, draggable);
           card = $("#card_discarded_pool");
        }

        card.data({
            'originalLeft': card.css('left'),
            'origionalTop': card.css('top')
        });

        if (draggable) {
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