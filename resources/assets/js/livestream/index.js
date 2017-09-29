import Publisher from './publisher';
import Subscriber from './subscriber';

window.LiveStream = {
    config: {
        port: 8081,
        app: 'live',
        protocol: 'ws',
        host: 'localhost',
        streamName: 'mystream',
        iceServers: [{ urls: 'stun:stun2.l.google.com:19302' }],
        mediaConstraints: { audio: true, video: true }
    },

    Publisher(params) {
        return new Publisher(params);
    },

    Subscriber(params) {
        return new Subscriber(params);
    }
}
