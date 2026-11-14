import { Component } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';

@Component({
  imports: [RouterLink, RouterLinkActive],
  selector: 'app-sidebar',
  styleUrl: './sidebar.css',
  templateUrl: './sidebar.html',
})
export class Sidebar {

   menuItems = [
    { label: 'Accueil', route: '/accueil', icon: 'home' },
    { label: 'Inventory', route: '/inventory', icon: 'inventory_2' },
    { label: 'Sales', route: '/sales', icon: 'point_of_sale' },
    { label: 'Stock', route: '/stock', icon: 'warehouse' },
    { label: 'Statistiques', route: '/statistiques', icon: 'bar_chart' },
    { label: 'Settings', route: '/settings', icon: 'settings' },
  ];

  // buttomMenu=[
  //   { label: 'Profile', route: '/profile' },
  //   { label: 'Help & Support', route: '/help', icon: 'Help & Support' }
  // ];


  logout() {
    // Appelle AuthService pour supprimer le token
    // puis rediriger vers login
  }
}
