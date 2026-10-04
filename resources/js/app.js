import Alpine from 'alpinejs';
import './toast';
import './pwa';
import { initProductCard } from './product-card';

window.Alpine = Alpine;
initProductCard(Alpine);
Alpine.start();

