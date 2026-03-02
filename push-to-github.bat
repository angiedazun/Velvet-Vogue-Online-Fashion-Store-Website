@echo off
echo ================================================
echo   Velvet Vogue - GitHub Upload Script
echo ================================================
echo.

cd /d "c:\xampp\htdocs\Velvet Vogue Online Fashion Store Website"

echo [1/6] Initializing git repository...
git init
echo Done.
echo.

echo [2/6] Staging all files...
git add .
echo Done.
echo.

echo [3/6] Creating commit...
git commit -m "Initial commit: Velvet Vogue Online Fashion Store - full project"
echo Done.
echo.

echo [4/6] Setting branch to main...
git branch -M main
echo Done.
echo.

echo [5/6] Adding remote origin...
git remote remove origin 2>nul
git remote add origin https://github.com/angiedazun/Velvet-Vogue-Online-Fashion-Store-Website.git
echo Done.
echo.

echo [6/6] Pushing to GitHub...
git push -u origin main
echo.

echo ================================================
echo   DONE! Check GitHub for your uploaded project.
echo   https://github.com/angiedazun/Velvet-Vogue-Online-Fashion-Store-Website
echo ================================================
echo.
pause
