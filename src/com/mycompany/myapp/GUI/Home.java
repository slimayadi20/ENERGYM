/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.myapp.GUI;

import com.codename1.ui.Button;
import com.codename1.ui.Form;
import com.codename1.ui.Label;
import com.codename1.ui.layouts.BoxLayout;


/**
 *
 * @author MSI
 */
public class Home extends Form {
    Form current;
    public Home() {

        current = this; 
        setTitle("Home");
        setLayout(BoxLayout.y());

        add(new Label("Choose an option"));

        Button btnlistrecla = new Button("List reclamation");
        Button btnaddrecla = new Button("Add reclamation");
        btnlistrecla.addActionListener(e-> new DisplayReclamation(current).show());
        btnaddrecla.addActionListener(e-> new addReclamation(current).show());
        addAll(btnlistrecla,btnaddrecla);

        
 

  
        

    }
}

    

