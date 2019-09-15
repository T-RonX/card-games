class HandHoverAnimator {
    constructor(card_container, card_width, card_height, trans_top) {
        this.card_container = card_container;
        this.card_width = card_width;
        this.card_height = card_height;
        this.trans_top = trans_top;
        this.deadzone_top = Math.ceil(trans_top * .1);
        this.deadzone_bottom = trans_top + this.deadzone_top;
    }

    setAnimations() {
        for (const card of this.card_container.getCards()) {
            card.mousemove(e => {
                let card = $(e.target);

                // Already hovering. Do not animate again.
                if (true === card.data('is_hover_animated')) {
                    return;
                }

                /// Card rotation angle inverse (for correcting current angle)
                let angle = MathHelper.degreesToRadians(card.data('rotation_angle') * -1);

                // Get pointer coordinates within the card
                var rect = e.target.getBoundingClientRect();
                var x = e.clientX - rect.x;
                var y = e.clientY - rect.y;

                // Correct the angle of the coordinate.
                let newY = y * Math.cos(angle) + x * Math.sin(angle);

                // Get the vertical angle length of the card.
                var angle_length = (this.card_width * Math.sin(angle) / .89399); // Short triangle side length. Math.sin(90) = .89399

                // Correct the y offset.
                if (angle > 0) {
                    newY -= angle_length;
                }

                // Show hover animation whenever the point is within the 1ed bound.
                if ((newY > this.deadzone_top && ((this.card_height - newY) > this.deadzone_bottom)) && !card.data('dragger').isDragging()) {
                    card.data('is_hover_animated', true);
                    card.animate({transform: '+=translate(0, -' + this.trans_top + 'px) '}, 67);
                }

            });

            card.hover(
                null,
                e => {
                    let card = $(e.target);
                    if (card.data('is_hover_animated') && !card.data('dragger').isDragging()) {
                        card.animate({transform: '+=translate(0, ' + this.trans_top + 'px) '}, 34);
                        card.data('is_hover_animated', false);
                    }
                });
        }
    }
}