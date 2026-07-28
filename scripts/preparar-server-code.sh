# Atualizar sistema e instalar Docker
sudo apt update && sudo apt upgrade -y
curl -fsSL [https://get.docker.com](https://get.docker.com) | bash
sudo systemctl enable --now docker
sudo apt install -y docker-compose-plugin git