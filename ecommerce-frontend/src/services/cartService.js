import api from '../api/axios'

export const cartService = {
  // Récupère les données actuelles du panier depuis le serveur
  getCart: async () => {
    const response = await api.get('/cart')
    return response.data
  },

  // Ajoute un produit au panier avec une quantité spécifiée
  addToCart: async (productId, quantity) => {
    const response = await api.post('/cart/add', {
      product_id: productId,
      quantity,
    })
    return response.data
  },

  // Modifie la quantité d'un produit déjà présent dans le panier
  updateCartItem: async (itemId, quantity) => {
    const response = await api.put(`/cart/items/${itemId}`, { quantity })
    return response.data
  },

  // Supprime définitivement un produit spécifique du panier
  removeCartItem: async (itemId) => {
    const response = await api.delete(`/cart/items/${itemId}`)
    return response.data
  },

  // Vide complètement le panier en une seule fois
  clearCart: async () => {
    const response = await api.delete('/cart/clear')
    return response.data
  },
}
