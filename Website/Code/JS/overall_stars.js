// overall-stars.js

document.addEventListener("DOMContentLoaded", function () {
  const overallStarsElements = document.querySelectorAll(".overall-stars");

  overallStarsElements.forEach(async (el) => {
    const site = el.getAttribute("data-site");
    try {
      const response = await fetch(`get_ratings.php?site=${encodeURIComponent(site)}`);
      const data = await response.json();

      const avg = ((parseFloat(data.community) || 0) + (parseFloat(data.professional) || 0)) / 2;
      const rounded = Math.round(avg);
      el.innerHTML = generateStars(rounded);
    } catch (error) {
      el.innerHTML = "Error loading rating";
      console.error("Failed to load overall rating:", error);
    }
  });

  function generateStars(count) {
    let stars = "";
    for (let i = 0; i < 5; i++) {
      stars += `<i class="fa-star fa ${i < count ? 'fas filled' : 'far'}"></i>`;
    }
    return stars;
  }
});
