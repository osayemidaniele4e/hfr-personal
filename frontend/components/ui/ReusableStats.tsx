import React from "react";

interface StatItem {
  title: string;
  value: string | number;
  description?: string;
  icon?: React.ReactNode;
  figureClassName?: string;
  valueClassName?: string;
  descClassName?: string;
}

interface ReusableStatsProps {
  stats: StatItem[];
}

const ReusableStats: React.FC<ReusableStatsProps> = ({ stats }) => {
  return (
    <div className="stats stats-vertical md:stats-horizontal shadow px-[2rem] md:px-[0] mx-[6rem] md:mx-[0]">
      {stats.map((stat, index) => (
        <div key={index} className="stat">
          {stat.icon && (
            <div className={`stat-figure ${stat.figureClassName || ""}`}>
              {stat.icon}
            </div>
          )}
          <div className="stat-title">{stat.title}</div>
          <div className={`stat-value ${stat.valueClassName || ""}`}>
            {stat.value}
          </div>
          {stat.description && (
            <div className={`stat-desc ${stat.descClassName || ""}`}>
              {stat.description}
            </div>
          )}
        </div>
      ))}
    </div>
  );
};

export default ReusableStats;
