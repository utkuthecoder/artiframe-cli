#!/bin/bash
echo "======================================"
echo " ArtiFrame Ecosystem macOS Installer"
echo "======================================"

if ! command -v brew &> /dev/null; then
    echo "[-] Homebrew not found. Installing Homebrew..."
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
fi

echo "[+] Installing PHP and Node.js via Homebrew..."
brew install php node

echo "[+] Installing ArtiFrame CLI globally..."
npm install -g @artilingo/artiframe-cli

echo "======================================"
echo "✅ Installation Complete!"
echo "   Run 'artiframe devops' in your terminal to launch the studio."
echo "======================================"
