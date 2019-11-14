class DiscardedCard
{
    static createCard(card_id, draggable) {
        $('#discarded-pool').append(
            `<div id="card-discarded-pool" data-card-id="${card_id}" class="card table ${CardHelper.getValueFromId(card_id)} ${draggable ? 'draggable' : ''}"></div>`
        );
    }

    static removeCard() {
        $('#card-discarded-pool').remove();
    }

    static resetCard(card_id, draggable) {
        $("#card-discarded-pool").remove();

        this.createCard(card_id, draggable);
        const card = $("#card-discarded-pool");

        card.data({
            'originalLeft': card.css('left'),
            'originalTop': card.css('top')
        });

        if (draggable) {
            card.draggable({
                // drag: function (event, ui) {
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
                        'top': $(this).data('originalTop')
                    });
                    dragCheck = false;
                    $('.card-hand-dropper').hide();
                }
            });
        }
    }

    static disableDraggable() {
        const card = $('#card-discarded-pool');

        if (card.data('uiDraggable')) {
            card.draggable('disable');
        }
    }

    static enableDraggable() {
        const card = $('#card-discarded-pool');

        if (card.data('uiDraggable')) {
            card.draggable('enable');
        }
    }
}