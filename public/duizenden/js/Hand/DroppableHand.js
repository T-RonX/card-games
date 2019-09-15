class DroppableHand {
    constructor(card_container, path_draw_from_discarded, path_draw_from_undrawn, hand) {
        this.path_draw_from_discarded = path_draw_from_discarded;
        this.path_draw_from_undrawn = path_draw_from_undrawn;
        this.card_container = card_container;
        this.is_hand_dropping = false;
    }

    makeDropable() {
        this.card_container.getContainer().find('.card_hand_dropper').droppable({
            hoverClass: 'highlight',
            drop: async (e, ui) => {
                //console.log(ui.draggable.data('card-order') + ' to ' + $(this).data('card-order'));

                if (drag_source === 'discarded_pool') {
                    let cards = [];

                    this.card_container.getContainer().filter('.selected').each(function (item) {
                        cards.push($(this).data('card-id'));
                    });

                    if (cards.length) {
                        $.post(this.path_draw_from_discarded.replace('000', cards.join()), null, function (data) {
                            location.reload();
                        });
                    } else {
                        $.post(this.path_draw_from_discarded, null, function (data) {
                            location.reload();
                        });
                    }
                }

                if (drag_source === 'hand_container' && !this.is_hand_dropping) {
                    this.is_hand_dropping = true;
                    let container = this.card_container.getContainer();

                    let target = $(e.target);
                    let target_id = target.data('card-order');
                    let source_id = ui.draggable.data('card-order');
                    //await new Promise(resolve => setTimeout(resolve, 0));
                    $.post('/duizenden/reorder-card/000/000'.replace('000', source_id).replace('000', target_id), null, (data) => {
                        location.reload();
                        return;

                        // //alert(source_id + ' ' + target_id + ' ' + data.success);
                        // let elm_source = container.find('[data-card-order=' + source_id + ']');
                        // let elm_target = container.find('[data-card-order=' + target_id + ']');
                        //
                        // if (elm_target.data('card-order') !== undefined) {
                        //     elm_source.insertAfter(elm_target);
                        // } else {
                        //     container.find('[data-card-order=1]').insertBefore(elm_target);
                        //     //alert(this.cards.filter('[data-card-order=1]').data('card-order'));
                        // }
                        //
                        // this.cards = $('.card_hand');
                        //
                        //
                        // container.empty();
                        //
                        // let new_order = 1;
                        // this.cards.each(function (i, elem) {
                        //     // alert($(elem).data('card-id'));
                        //     let card_id = $(elem).data('card-id');
                        //     //alert($(elem).data('card-order'));
                        //     container.append('<div data-card-order="' + new_order + '" data-card-id="' + card_id + '" class="card_hand draggable" style="background-image: url(\'/duizenden/cards/' + card_id.toLowerCase() + '.svg\')"></div>');
                        //     ++new_order;
                        // });
                        //
                        // this.hand.setCards($('.card_hand'));
                        // this.hand.initialize();
                        //
                        //
                        // //elm_source.css('opacity', '1'); // @TODO fix me!!

                    });
                    this.is_hand_dropping = false;
                }

                if (drag_source === 'undrawn_pool') {

                    $.post(this.path_draw_from_undrawn, null, function (data) {
                        location.reload();
                    });
                }
            }
        });
    }
}