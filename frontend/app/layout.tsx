import MainLayout from "./MainLayout";
export const revalidate = 3600;
async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return <MainLayout>{children}</MainLayout>;
}
export default RootLayout;
