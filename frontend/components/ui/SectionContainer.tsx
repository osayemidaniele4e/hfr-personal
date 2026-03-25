const SectionContainer = ({
  children,
  className,
}: {
  children: React.ReactNode;
  className?: string;
}) => {
  return (
    <div className="max-w-[1280px] mx-auto px-4 sm:px-6 md:px-8  outline outline-1 outline-red-500">
      {children}
    </div>
  );
};

export default SectionContainer;
