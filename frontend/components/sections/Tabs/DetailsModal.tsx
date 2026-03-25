import React from "react";

// const DetailsModal = ({ row, onClose }) => {

const DetailsModal: React.FC<{ row: any; onClose: () => void }> = ({
  row,
  onClose,
}) => {
  if (!row) return null; // Prevent rendering if no data

  return (
    <div
      className="fixed inset-0 flex justify-start z-50 bg-black bg-opacity-40"
      onClick={onClose}
    >
      {/* Offcanvas Panel */}
      <div className="bg-white w-[95%] sm:w-[90%] md:w-[400px] lg:w-[400px] xl:w-[400px] max-w-full h-full shadow-xl overflow-y-auto relative z-60">
        {/* Header */}
        <div className="flex justify-between items-center border-b pb-3 mb-4 sticky top-0 bg-white z-10">
          <h3 className="text-2xl font-bold">Facility Details</h3>
          <button
            onClick={onClose}
            className="text-gray-500 hover:text-gray-800 text-3xl"
          >
            ✕
          </button>
        </div>

        {/* Body */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 p-5">
          <div className="table-responsive">
            <table className="table mb-0 w-full border-top table-bordered text-nowrap">
              <tbody>
                <tr>
                  <th scope="row">
                    <h3 className="text-2xl font-bold">Facility Details</h3>
                  </th>
                  <td>
                    <button
                      onClick={onClose}
                      className="text-gray-500 hover:text-gray-800 text-3xl"
                    >
                      ✕
                    </button>
                  </td>
                </tr>

                <tr>
                  <th scope="row">Facility Name</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        // maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.facility_name}
                  </td>
                </tr>
                <tr>
                  <th scope="row">Facility Code</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.unique_id}
                  </td>
                </tr>

                <tr>
                  <th scope="row">Facility Email Address</th>
                  <td className="break-words overflow-wrap break-all">
                    {row.email_address ?? "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">Facility Contact Number</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.phone_number ? row.phone_number : "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">Facility Location</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.physical_location ?? "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">Registration Number</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.registration_no ? row.registration_no : "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">Facility Level</th>
                  <td>
                    <span
                      className={`badge ${
                        row.facility_level_name === "Primary"
                          ? "bg-success"
                          : row.facility_level_name === "Secondary"
                          ? "bg-info"
                          : row.facility_level_name === "Tertiary"
                          ? "bg-danger"
                          : "bg-secondary"
                      }`}
                    >
                      {row.facility_level_name}
                    </span>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Ownership</th>
                  <td>
                    <span
                      className={`badge ${
                        row.ownership_name === "Public"
                          ? "bg-success"
                          : row.ownership_name === "Private"
                          ? "bg-danger"
                          : "bg-secondary"
                      }`}
                    >
                      {row.ownership_name}
                    </span>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Ownership Type</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.ownership_type ?? "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">License Status</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.license_status_name ?? "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">Status</th>
                  <td
                    style={
                      {
                        wordBreak: "break-all",
                        whiteSpace: "normal",
                        // maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.publish_note ?? "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">State</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.state_name ?? "N/A"}
                  </td>
                </tr>
                <tr>
                  <th scope="row">LGA</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.lga_name ?? "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">Ward</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.ward_name ?? "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">Latitude</th>
                  <td
                    style={
                      {
                        wordBreak: "break-word",
                        whiteSpace: "normal",
                        maxWidth: "300px",
                      } as React.CSSProperties
                    }
                  >
                    {row.latitude ? row.latitude : "N/A"}
                  </td>
                </tr>

                <tr>
                  <th scope="row">Longitude</th>
                  <td>{row.longitude ? row.longitude : "N/A"}</td>
                </tr>

                <tr>
                  <th scope="row">Number of Midwifes</th>
                  <td>{row.midwifes ?? "N/A"}</td>
                </tr>

                <tr>
                  <th scope="row">Number of Doctors</th>
                  <td>{row.doctors ?? "N/A"}</td>
                </tr>
                <tr>
                  <th scope="row">Number of Dentist</th>
                  <td>{row.dentist ?? "N/A"}</td>
                </tr>

                <tr>
                  <th scope="row">Number of Dentist Technicians</th>
                  <td>{row.dental_technicians ?? "N/A"}</td>
                </tr>

                <tr>
                  <th scope="row">Laboratory Scientists</th>
                  <td>{row.lab_scientists ?? "N/A"}</td>
                </tr>

                <tr>
                  <th scope="row">Laboratory Technicians</th>
                  <td>{row.lab_technicians ?? "N/A"}</td>
                </tr>

                <tr>
                  <th scope="row">Number of Bed</th>
                  <td>{row.beds ?? "N/A"}</td>
                </tr>
              </tbody>
            </table>
          </div>

          {/* Add more fields if needed */}
        </div>
      </div>
    </div>
  );
};

export default DetailsModal;
