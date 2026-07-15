import ConversationDrawer from './drawer';

console.log('Conversation loaded');

document.addEventListener('DOMContentLoaded', () => {
    console.log('Creating drawer');
    new ConversationDrawer();
});