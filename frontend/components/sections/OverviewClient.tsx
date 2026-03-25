"use client";

import React from "react";
import { useEffect } from "react";

export default function OverviewClient() {
  useEffect(() => {
    const divElement = document.getElementById("viz1765376005237");
    if (!divElement) return;

    const vizElement = divElement.getElementsByTagName("object")[0];

    const updateSize = () => {
      // Set width to 100% of container
      vizElement.style.width = "100%";
      
      // Use 0.75 aspect ratio as per Tableau's recommended sizing
      vizElement.style.height = (divElement.offsetWidth * 0.75) + "px";
    };

    // Initial size
    updateSize();

    // Load Tableau JS API dynamically
    const scriptElement = document.createElement("script");
    scriptElement.src = "https://public.tableau.com/javascripts/api/viz_v1.js";
    if (vizElement.parentNode) {
      vizElement.parentNode.insertBefore(scriptElement, vizElement);
    }

    // Update on window resize with debounce
    let resizeTimer: NodeJS.Timeout;
    const handleResize = () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(updateSize, 250);
    };

    window.addEventListener("resize", handleResize);
    return () => {
      window.removeEventListener("resize", handleResize);
      clearTimeout(resizeTimer);
    };
  }, []);

  return (
    <div className="w-full mt-20">
      {/* Removed px-4 and max-w to allow full width */}
      <div
        className="tableauPlaceholder w-full"
        id="viz1765376005237"
        style={{ position: "relative" }}
      >
        <noscript>
          <a href="#">
            <img
              alt="HFR"
              src="https://public.tableau.com/static/images/He/HealthFacilityRegistryDashboardNew/HFR/1_rss.png"
              style={{ border: "none", width: "100%" }}
            />
          </a>
        </noscript>

        <object className="tableauViz w-full" style={{ display: "none" }}>
          <param name="host_url" value="https%3A%2F%2Fpublic.tableau.com%2F" />
          <param name="embed_code_version" value="3" />
          <param name="site_root" value="" />
          <param name="name" value="HealthFacilityRegistryDashboardNew/HFR" />
          <param name="tabs" value="no" />
          <param name="toolbar" value="yes" />
          <param
            name="static_image"
            value="https://public.tableau.com/static/images/He/HealthFacilityRegistryDashboardNew/HFR/1.png"
          />
          <param name="animate_transition" value="yes" />
          <param name="display_static_image" value="yes" />
          <param name="display_spinner" value="yes" />
          <param name="display_overlay" value="yes" />
          <param name="display_count" value="yes" />
          <param name="language" value="en-US" />
        </object>
      </div>
    </div>
  );
}