import api from '../api/axios'

export const categoryService = {
  // Récupère une liste paginée de catégories depuis le serveur (page 1 par défaut)
  getCategories: async (page = 1) => {
    const response = await api.get(`/categories?page=${page}`)
    return response.data
  },

  // Récupère les détails d'une catégorie spécifique via son ID
  getCategoryById: async (id) => {
    const response = await api.get(`/categories/${id}`)
    return response.data
  },

  // Crée une nouvelle catégorie sur le serveur avec les données fournies
  createCategory: async (data) => {
    const response = await api.post('/categories', data)
    return response.data
  },

  // Met à jour les informations d'une catégorie existante via son ID
  updateCategory: async (id, data) => {
    const response = await api.put(`/categories/${id}`, data)
    return response.data
  },

  // Supprime définitivement une catégorie spécifique du serveur via son ID
  deleteCategory: async (id) => {
    const response = await api.delete(`/categories/${id}`)
    return response.data
  },

  // Récupère un grand nombre de catégories (jusqu'à 100) en une seule fois, avec gestion d'erreur
  getAllCategories: async () => {
    try {
      const response = await api.get('/categories?per_page=100')
      return response.data.data || []
    } catch {
      return []
    }
  },
}
