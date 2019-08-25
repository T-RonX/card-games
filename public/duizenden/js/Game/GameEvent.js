class GameEvent {
    constructor(connection, message_handler) {
        this.connection = connection;
        this.message_handler = message_handler
    }

    initialize() {
        this.connection.connect((e) => {
            this.received(e)
        });
    }

    received(e) {
        this.message_handler.handle(e);
    }
}