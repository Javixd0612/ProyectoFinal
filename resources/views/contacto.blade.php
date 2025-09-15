<x-app-layout>
    <div class="container mx-auto max-w-2xl mt-10 bg-[#111] p-8 rounded-2xl shadow-lg border border-cyan-500">
        <h1 class="text-3xl font-bold text-cyan-400 text-center mb-6">Contáctanos</h1>

        <p class="text-gray-300 text-center mb-6">
            ¿Tienes dudas o quieres más información sobre <strong>Tecno Juegos</strong>?  
            Escríbenos y te responderemos lo más pronto posible.
        </p>

        <form action="#" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="nombre" class="block text-cyan-300">Nombre</label>
                <input type="text" id="nombre" name="nombre"
                       class="w-full px-4 py-2 rounded-lg bg-[#1e1e1e] text-white border border-cyan-500 focus:outline-none focus:ring focus:ring-cyan-400">
            </div>

            <div>
                <label for="email" class="block text-cyan-300">Correo</label>
                <input type="email" id="email" name="email"
                       class="w-full px-4 py-2 rounded-lg bg-[#1e1e1e] text-white border border-cyan-500 focus:outline-none focus:ring focus:ring-cyan-400">
            </div>

            <div>
                <label for="mensaje" class="block text-cyan-300">Mensaje</label>
                <textarea id="mensaje" name="mensaje" rows="4"
                          class="w-full px-4 py-2 rounded-lg bg-[#1e1e1e] text-white border border-cyan-500 focus:outline-none focus:ring focus:ring-cyan-400"></textarea>
            </div>

            <button type="submit" class="w-full neon-btn">Enviar Mensaje</button>
        </form>

        <div class="flex justify-center gap-6 mt-8">
            <a href="https://facebook.com" target="_blank" class="text-blue-500 hover:text-blue-300">Facebook</a>
            <a href="https://instagram.com" target="_blank" class="text-pink-500 hover:text-pink-300">Instagram</a>
            <a href="https://twitter.com" target="_blank" class="text-sky-400 hover:text-sky-200">X (Twitter)</a>
        </div>
    </div>
</x-app-layout>
