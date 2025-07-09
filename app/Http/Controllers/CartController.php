<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Mostrar el carrito del usuario
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $cart = $user->getActiveCart();
            
            $cartItems = $cart->items()->with(['game.category'])->get();
            
            // Calcular subtotal
            $subtotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });
            
            // IGV del 18%
            $igv = $subtotal * 0.18;
            
            // Total = subtotal + IGV
            $total = $subtotal + $igv;
            
            return view('cart.index', compact('cartItems', 'subtotal', 'igv', 'total'));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar carrito: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Error al cargar el carrito');
        }
    }

    /**
     * Agregar juego al carrito
     */
    public function add(Request $request, Game $game)
    {
        try {
            $user = Auth::user();
            
            Log::info("Usuario {$user->id} intentando agregar juego {$game->id} al carrito");
            
            // Verificar que el juego esté activo
            if (!$game->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este juego no está disponible'
                ], 400);
            }
            
            // Verificar que el usuario no tenga ya el juego en su biblioteca
            if ($user->ownsGame($game->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya tienes este juego en tu biblioteca'
                ], 400);
            }
            
            // Obtener o crear carrito activo
            $cart = $user->getActiveCart();
            
            // Verificar si el juego ya está en el carrito
            $existingItem = $cart->items()->where('game_id', $game->id)->first();
            
            if ($existingItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este juego ya está en tu carrito'
                ], 400);
            }
            
            // Agregar el juego al carrito
            DB::beginTransaction();
            
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'game_id' => $game->id,
                'quantity' => 1,
                'price' => $game->price,
            ]);
            
            DB::commit();
            
            // Obtener el nuevo contador del carrito
            $cartCount = $cart->fresh()->getTotalItems();
            
            Log::info("Juego {$game->title} agregado exitosamente al carrito del usuario {$user->id}");
            
            return response()->json([
                'success' => true,
                'message' => "'{$game->title}' agregado al carrito",
                'cart_count' => $cartCount,
                'item_id' => $cartItem->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agregar al carrito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar el juego al carrito'
            ], 500);
        }
    }

    /**
     * Eliminar item del carrito
     */
    public function remove(CartItem $item)
    {
        try {
            $user = Auth::user();
            
            // Verificar que el item pertenezca al usuario
            if ($item->cart->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este item'
                ], 403);
            }
            
            $gameTitle = $item->game->title;
            
            DB::beginTransaction();
            $item->delete();
            DB::commit();
            
            // Obtener el nuevo contador del carrito
            $cart = $user->getActiveCart();
            $cartCount = $cart->getTotalItems();
            
            return response()->json([
                'success' => true,
                'message' => "'{$gameTitle}' eliminado del carrito",
                'cart_count' => $cartCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar del carrito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el juego del carrito'
            ], 500);
        }
    }

    /**
     * Limpiar todo el carrito
     */
    public function clear()
    {
        try {
            $user = Auth::user();
            $cart = $user->getActiveCart();
            
            $itemCount = $cart->getTotalItems();
            
            DB::beginTransaction();
            $cart->items()->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$itemCount} juegos del carrito",
                'cart_count' => 0
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al limpiar carrito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar el carrito'
            ], 500);
        }
    }

    /**
     * Obtener contador del carrito (AJAX)
     */
    public function getCount()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'cart_count' => 0
                ]);
            }
            
            $cart = $user->getActiveCart();
            $cartCount = $cart->getTotalItems();
            
            return response()->json([
                'success' => true,
                'cart_count' => $cartCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener contador: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'cart_count' => 0
            ]);
        }
    }

    // ... resto de métodos sin cambios ...
    
    /**
     * Actualizar cantidad de un item
     */
    public function updateQuantity(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);
        
        try {
            $user = Auth::user();
            
            // Verificar que el item pertenezca al usuario
            if ($item->cart->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para modificar este item'
                ], 403);
            }
            
            DB::beginTransaction();
            $item->update(['quantity' => $request->quantity]);
            DB::commit();
            
            // Calcular nuevo subtotal del item
            $subtotal = $item->price * $item->quantity;
            
            // Obtener nuevo total del carrito
            $cart = $user->getActiveCart();
            $cartTotal = $cart->getTotalPrice();
            
            return response()->json([
                'success' => true,
                'message' => 'Cantidad actualizada',
                'item_subtotal' => number_format($subtotal, 2),
                'cart_total' => number_format($cartTotal, 2)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar cantidad: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la cantidad'
            ], 500);
        }
    }

    /**
     * Proceder al checkout (redirigir a pago)
     */
    public function checkout()
    {
        try {
            $user = Auth::user();
            $cart = $user->getActiveCart();
            $cartItems = $cart->items()->with('game')->get();
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
            }
            
            // IMPORTANTE: Redirigir al pago del carrito completo
            return redirect()->route('payment.cart.form')
                        ->with('success', 'Procediendo al checkout con ' . $cartItems->count() . ' juego(s)');
            
        } catch (\Exception $e) {
            Log::error('Error en checkout: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Error al proceder al checkout');
        }
    }

    /**
     * Verificar si un juego está en el carrito (AJAX)
     */
    public function isInCart(Game $game)
    {
        try {
            $user = Auth::user();
            $cart = $user->getActiveCart();
            
            $isInCart = $cart->items()->where('game_id', $game->id)->exists();
            
            return response()->json([
                'success' => true,
                'is_in_cart' => $isInCart
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al verificar carrito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'is_in_cart' => false
            ]);
        }
    }

    /**
     * Obtener resumen del carrito (AJAX)
     */
    public function getSummary()
    {
        try {
            $user = Auth::user();
            $cart = $user->getActiveCart();
            $cartItems = $cart->items()->with(['game.category'])->get();
            
            $summary = [
                'items_count' => $cartItems->count(),
                'total_quantity' => $cartItems->sum('quantity'),
                'subtotal' => $cart->getTotalPrice(),
                'total' => $cart->getTotalPrice(),
                'items' => $cartItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'game_title' => $item->game->title,
                        'game_image' => asset($item->game->image_url),
                        'price' => number_format($item->price, 2),
                        'quantity' => $item->quantity,
                        'subtotal' => number_format($item->price * $item->quantity, 2)
                    ];
                })
            ];
            
            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener resumen: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen del carrito'
            ]);
        }
    }

    /**
     * Verificar disponibilidad de un juego antes de agregarlo
     */
    public function checkAvailability(Game $game)
    {
        try {
            $user = Auth::user();
            
            // Verificaciones
            $checks = [
                'is_active' => $game->is_active,
                'already_owned' => !$user->ownsGame($game->id),
                'not_in_cart' => !$user->getActiveCart()->items()->where('game_id', $game->id)->exists()
            ];
            
            $available = array_reduce($checks, function($carry, $check) {
                return $carry && $check;
            }, true);
            
            return response()->json([
                'success' => true,
                'available' => $available,
                'checks' => $checks,
                'message' => $available ? 'Juego disponible para agregar' : 'Juego no disponible'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al verificar disponibilidad: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'available' => false,
                'message' => 'Error al verificar disponibilidad'
            ]);
        }
    }
}