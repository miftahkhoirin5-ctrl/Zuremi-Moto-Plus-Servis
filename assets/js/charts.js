/**
 * MotoTrust & Bayou Automotive - Custom Canvas Chart Engine
 * Zero external dependencies, offline-ready, retina-sharp, dark/light aware.
 */

class MotoCharts {
  static drawWeeklyRevenue(canvasId, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();

    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);

    const width = rect.width;
    const height = rect.height;
    const padding = { top: 20, right: 20, bottom: 35, left: 45 };

    const labels = data.labels || ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    const values = data.values || [20000, 31000, 26000, 20000, 37000, 48000, 44000];
    const maxVal = 50000;

    const isLight = document.documentElement.getAttribute('data-theme') === 'light';
    const gridColor = isLight ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.06)';
    const textColor = isLight ? '#64748b' : '#8492a6';
    const barPrimary = isLight ? '#0284c7' : '#f97316';
    const barSecondary = isLight ? '#38bdf8' : '#fb923c';

    ctx.clearRect(0, 0, width, height);

    // Draw Y grid lines (10K, 20K, 30K, 40K, 50K)
    const ySteps = [0, 10000, 20000, 30000, 40000, 50000];
    ctx.font = '11px sans-serif';
    ctx.fillStyle = textColor;
    ctx.textAlign = 'right';
    ctx.textBaseline = 'middle';

    const plotHeight = height - padding.top - padding.bottom;
    const plotWidth = width - padding.left - padding.right;

    ySteps.forEach(step => {
      if (step === 0) return;
      const y = padding.top + plotHeight - (step / maxVal) * plotHeight;

      ctx.strokeStyle = gridColor;
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.moveTo(padding.left, y);
      ctx.lineTo(width - padding.right, y);
      ctx.stroke();

      ctx.fillText((step / 1000) + 'K', padding.left - 10, y);
    });

    // Draw Bars
    const barWidth = Math.min(28, (plotWidth / values.length) * 0.55);
    const stepX = plotWidth / values.length;

    values.forEach((val, idx) => {
      const x = padding.left + idx * stepX + (stepX - barWidth) / 2;
      const barH = (val / maxVal) * plotHeight;
      const y = padding.top + plotHeight - barH;

      // Gradient bar
      const grad = ctx.createLinearGradient(0, y, 0, y + barH);
      grad.addColorStop(0, barSecondary);
      grad.addColorStop(1, barPrimary);

      ctx.fillStyle = grad;

      // Rounded bar top
      const radius = 4;
      ctx.beginPath();
      ctx.moveTo(x, y + barH);
      ctx.lineTo(x, y + radius);
      ctx.quadraticCurveTo(x, y, x + radius, y);
      ctx.lineTo(x + barWidth - radius, y);
      ctx.quadraticCurveTo(x + barWidth, y, x + barWidth, y + radius);
      ctx.lineTo(x + barWidth, y + barH);
      ctx.closePath();
      ctx.fill();

      // X Label
      ctx.fillStyle = textColor;
      ctx.textAlign = 'center';
      ctx.textBaseline = 'top';
      ctx.fillText(labels[idx], x + barWidth / 2, height - padding.bottom + 10);
    });
  }

  static drawDonut(canvasId, segments) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();

    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);

    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    const radius = Math.min(centerX, centerY) - 15;
    const innerRadius = radius * 0.6;

    let total = segments.reduce((acc, s) => acc + s.percentage, 0);
    let startAngle = -Math.PI / 2;

    segments.forEach(seg => {
      const sliceAngle = (seg.percentage / total) * 2 * Math.PI;

      ctx.beginPath();
      ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
      ctx.arc(centerX, centerY, innerRadius, startAngle + sliceAngle, startAngle, true);
      ctx.closePath();
      ctx.fillStyle = seg.color;
      ctx.fill();

      startAngle += sliceAngle;
    });
  }
}
window.MotoCharts = MotoCharts;

