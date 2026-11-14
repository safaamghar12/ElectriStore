import { Routes } from '@angular/router';
import { Dashboard } from './dashboard/dashboard';



export const routes: Routes = [
  {
    path: '',
    component: Dashboard,
    children: [
      {
        path: '',
        loadComponent: () =>
          import('./pages/inventory/inventory')
            .then(m => m.Inventory)
      },
      {
        path: 'inventory',
        loadComponent: () =>
          import('./pages/inventory/inventory')
            .then(m => m.Inventory)
      },
      {
        path: 'accueil',
        loadComponent : ()=>
          import('./pages/accueil/accueil')
        .then(m=>m.Accueil)
      },
      {
        path: 'profile',
        loadComponent : ()=>
          import('./pages/profile/profile')
        .then(m=>m.Profile)
      },
      {
        path: 'sales',
        loadComponent : ()=>
          import('./pages/sales/sales')
        .then(m=>m.Sales)
      },
      {
        path: 'settings',
        loadComponent : ()=>
          import ('./pages/settings/settings')
        .then(m=>m.Settings)
      },
      {
        path : 'statistiques',
        loadComponent : () => 
          import('./pages/statistiques/statistiques')
          .then(m=>m.Statistiques)
      },
      {
        path : 'stock',
        loadComponent: ()=>
          import('./pages/stock/stock')
        .then(m=>m.Stock)
      },
      {
        path :'help',
        loadComponent: ()=>
          import('./pages/help/help')
        .then(m=>m.Help)
      }
    ],
  },
];
