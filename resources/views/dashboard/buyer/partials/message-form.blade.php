<form id="chatForm" class="flex gap-3">
    <input type="hidden" id="activeThreadId" value="{{ $thread_id }}">
    
    <input type="text" name="text" placeholder="Type your message..."
        class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">

    <button type="submit"
        class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
        Send
    </button>
</form>
