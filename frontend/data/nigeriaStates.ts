// GeoJSON data for Nigeria states
export default {
    type: "FeatureCollection",
    features: [
      {
        type: "Feature",
        properties: { name: "Lagos" },
        geometry: {
          type: "Polygon",
          coordinates: [[[3.0, 6.3], [3.4, 6.3], [3.4, 6.7], [3.0, 6.7], [3.0, 6.3]]]
        }
      },
      {
        type: "Feature",
        properties: { name: "Kano" },
        geometry: {
          type: "Polygon",
          coordinates: [[[8.4, 11.7], [8.8, 11.7], [8.8, 12.1], [8.4, 12.1], [8.4, 11.7]]]
        }
      },
      {
        type: "Feature",
        properties: { name: "FCT" },
        geometry: {
          type: "Polygon",
          coordinates: [[[7.1, 8.7], [7.5, 8.7], [7.5, 9.1], [7.1, 9.1], [7.1, 8.7]]]
        }
      },
      {
        type: "Feature",
        properties: { name: "Rivers" },
        geometry: {
          type: "Polygon",
          coordinates: [[[6.7, 4.6], [7.1, 4.6], [7.1, 5.0], [6.7, 5.0], [6.7, 4.6]]]
        }
      },
      {
        type: "Feature",
        properties: { name: "Oyo" },
        geometry: {
          type: "Polygon",
          coordinates: [[[3.6, 7.8], [4.0, 7.8], [4.0, 8.2], [3.6, 8.2], [3.6, 7.8]]]
        }
      }
    ]
  };