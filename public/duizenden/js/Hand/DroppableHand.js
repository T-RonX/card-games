class DroppableHand {
    constructor(hand, path_draw_from_discarded, path_draw_from_undrawn) {
        this.path_draw_from_discarded = path_draw_from_discarded;
        this.path_draw_from_undrawn = path_draw_from_undrawn;
        this.hand = hand;
        this.is_hand_dropping = false;
    }

    makeDropable() {
        this.hand.getHandContainer().getContainer().find('.card_hand_dropper').droppable({
            hoverClass: 'highlight',
            drop: async (e, ui) => {
                //console.log(ui.draggable.data('card-order') + ' to ' + $(this).data('card-order'));

                if (drag_source === 'discarded_pool') {
                    let cards = [];

                    this.hand.getHandContainer().getContainer().find('.selected').each(function (item) {
                        cards.push($(this).data('card-id'));
                    });

                    if (cards.length) {
                        $.post(this.path_draw_from_discarded.replace('000', cards.join()), null, function (data) {
                            //location.reload();
                        });
                    } else {
                        $.post(this.path_draw_from_discarded, null, function (data) {
                            //location.reload();
                        });
                    }
                }

                if (drag_source === 'hand_container' && !this.is_hand_dropping) {
                    this.is_hand_dropping = true;

                    let target = $(e.target);
                    let target_id = target.data('card-order');
                    let source_id = ui.draggable.data('card-order');

                    if (target_id === undefined || source_id === undefined) {
                        return;
                    }

                    //await new Promise(resolve => setTimeout(resolve, 0));
                    this.hand.reorderCards(source_id, target_id);
                    $.post('/duizenden/reorder-card/000/000'.replace('000', source_id).replace('000', target_id), null, (data) => {
                        this.is_hand_dropping = false;
                    });
                }

                if (drag_source === 'undrawn_pool') {

                    $.post(this.path_draw_from_undrawn, null, function (data) {
                        //location.reload();
                    });
                }
            }
        });
    }
}