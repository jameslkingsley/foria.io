window.Stream = class Stream {
    constructor(userId) {
        this.userId = userId;
        this.fetch().then(r => {
            this.data = r.data;
            this.initializeSession();
        });
    }

    fetch() {
        return axios.get(`/watch/${this.userId}/show`);
    }

    handleError(error) {
        if (error) {
            console.error(error);
        }
    }

    initializeSession() {
        this.session = OT.initSession(Foria.openTokKey, this.data.broadcast.session_id);

        // Subscribe to a newly created stream
        this.session.on('streamCreated', event => {
            this.subscriber = this.session.subscribe(event.stream, 'stream-publisher', {
                insertMode: 'append',
                width: '100%',
                height: '640px'
            }, this.handleError);
        });

        // Connect to the session
        this.session.connect(this.data.token, error => {
            // If the connection is successful, publish to the session
            if (error) {
                this.handleError(error);
            } else {
                if (this.data.broadcast.is_mine) {
                    // Create a publisher
                    let publisher = OT.initPublisher('stream-publisher', {
                        insertMode: 'append',
                        width: '100%',
                        height: '640px'
                    }, this.handleError);

                    this.publisher = this.session.publish(publisher, this.handleError);
                }
            }
        });
    }

    close() {
        if (this.data.broadcast.is_mine) {
            this.session.unpublish(this.publisher);
        }

        // this.session.unsubscribe(this.subscriber);
        this.session.disconnect();
    }
}
