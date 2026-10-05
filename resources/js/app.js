import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import {
  Chart,
  ArcElement,
  LineElement,
  BarElement,
  PointElement,
  BarController,
  DoughnutController,
  LineController,
  PieController,
  CategoryScale,
  LinearScale,
  LogarithmicScale,
  RadialLinearScale,
  TimeScale,
  TimeSeriesScale,
  Decimation,
  Filler,
  Legend,
  Title,
  Tooltip,
  SubTitle
} from 'chart.js';

// Register Chart.js components
Chart.register(
  ArcElement,
  LineElement,
  BarElement,
  PointElement,
  BarController,
  DoughnutController,
  LineController,
  PieController,
  CategoryScale,
  LinearScale,
  LogarithmicScale,
  RadialLinearScale,
  TimeScale,
  TimeSeriesScale,
  Decimation,
  Filler,
  Legend,
  Title,
  Tooltip,
  SubTitle
);

window.Chart = Chart;
window.createIcons = createIcons;
window.lucideIcons = icons;

// Setup Lucide icons trigger
document.addEventListener('DOMContentLoaded', () => {
  createIcons({ icons });
});

// Alpine setup
window.Alpine = Alpine;
Alpine.start();

// Hook into Alpine mutations to re-render icons if needed
document.addEventListener('alpine:initialized', () => {
  createIcons({ icons });
});
