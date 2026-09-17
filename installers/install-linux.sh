#!/bin/bash
echo "======================================"
echo " ArtiFrame Ecosystem Linux Installer"
echo "======================================"

if command -v apt &> /dev/null; then
    echo "[+] Updating apt repositories..."
    sudo apt update
    echo "[+] Installing PHP, Node.js and NPM..."
    sudo apt install -y php-cli nodejs npm
elif command -v dnf &> /dev/null; then
    echo "[+] Installing PHP, Node.js and NPM..."
    sudo dnf install -y php-cli nodejs npm
else
    echo "[-] Unsupported package manager. Please install PHP and Node.js manually."
    exit 1
fi

echo "[+] Installing ArtiFrame CLI globally..."
sudo npm install -g @artilingo/artiframe-cli

echo "======================================"
echo "✅ Installation Complete!"
echo "   You can now launch the studio by double-clicking the ArtiFrame icon in your Applications menu,"
echo "   or by typing 'artiframe devops' in your terminal."
echo "======================================"
