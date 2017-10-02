import EventMap from './events';

class Subscriber {
    constructor(config) {
        this.config = config;
        this.redConfig = _.extend({}, LiveStream.config, this.config.driver);

        this.driver = new Red5.RTCSubscriber();
        this.driver.on('*', e => this.pipeEvent(e));

        this.start();
    }

    pipeEvent(e) {
        let method = EventMap[e.type];

        if (method in this) {
            this[method](e.data);
        }

        if (method in this.config) {
            this.config[method](e.data);
        }
    }

    start() {
        this.driver.init(this.redConfig)
            .then(() => this.driver.subscribe())
            .catch(error => {
                console.log('Could not play: ' + error);
            });
    }

    stop() {
        //
    }
}

export default Subscriber
