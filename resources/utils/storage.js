import CryptoJS from 'crypto-js';

const SECRET_KEY = 'tu_clave_secreta'; 
const SESSION_TIMEOUT = 3600000; // 1 hora en milisegundos (60 * 60 * 1000)

export const storage = {
  set(key, value) {
    const dataToSave = {
      content: value,
      timestamp: Date.now() // Guardamos el momento exacto del guardado
    };
    const encrypted = CryptoJS.AES.encrypt(JSON.stringify(dataToSave), SECRET_KEY).toString();
    localStorage.setItem(key, encrypted);
  },

  get(key) {
    const encrypted = localStorage.getItem(key);
    if (!encrypted) return null;

    try {
      const bytes = CryptoJS.AES.decrypt(encrypted, SECRET_KEY);
      const decryptedData = JSON.parse(bytes.toString(CryptoJS.enc.Utf8));

      // VERIFICACIÓN DE EXPIRACIÓN
      const ahora = Date.now();
      if (ahora - decryptedData.timestamp > SESSION_TIMEOUT) {
        console.warn("Sesión expirada");
        this.remove(key); // Borramos el dato expirado
        return null;
      }

      return decryptedData.content;
    } catch (error) {
      return null;
    }
  },

  remove(key) {
    localStorage.removeItem(key);
  }
};