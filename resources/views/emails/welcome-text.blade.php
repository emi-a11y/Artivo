Welcome to Artivo, {{ \Illuminate\Support\Str::before($user->name, ' ') }}!

Your Artivo account is ready. Artivo is a home for digital art made by independent artists, and now you can collect it.

- Browse artwork by category, genre and price
- Save favourites to your wishlist
- Choose a license and check out in a few clicks

Start browsing: {{ route('home') }}

You are receiving this because an Artivo account was created with {{ $user->email }}. If this wasn't you, you can ignore
this email.