window.Stream = class Stream {
    constructor(userId) {
        this.userId = userId;
        this.fetch().then(r => {
            this.data = r.data;
            console.log(this.data);
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
        let session = OT.initSession(this.data.apiKey, this.data.sessionId);

        // Subscribe to a newly created stream
        session.on('streamCreated', function(event) {
            session.subscribe(event.stream, 'stream-subscriber', {
                insertMode: 'append',
                width: '100%',
                height: '100%'
            }, this.handleError);
        });

        // Connect to the session
        session.connect(this.data.token, error => {
            // If the connection is successful, publish to the session
            if (error) {
                this.handleError(error);
            } else {
                if (this.data.role == 'publisher') {
                    // Create a publisher
                    let publisher = OT.initPublisher('stream-publisher', {
                        insertMode: 'append',
                        width: '100%',
                        height: '640px'
                    }, this.handleError);

                    session.publish(publisher, this.handleError);
                }
            }
        });
    }
}
