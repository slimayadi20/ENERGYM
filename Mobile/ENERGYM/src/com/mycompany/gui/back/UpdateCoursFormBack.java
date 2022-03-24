/*
 * Copyright (c) 2016, Codename One
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
package com.mycompany.gui.back;

import com.codename1.components.ScaleImageLabel;
import com.codename1.ui.Button;
import com.codename1.ui.Command;
import com.codename1.ui.Component;
import com.codename1.ui.Dialog;
import com.codename1.ui.Display;
import com.codename1.ui.Form;
import com.codename1.ui.Image;
import com.codename1.ui.Label;
import com.codename1.ui.TextField;
import com.codename1.ui.Toolbar;
import com.codename1.ui.events.ActionEvent;
import com.codename1.ui.events.ActionListener;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.layouts.FlowLayout;
import com.codename1.ui.layouts.GridLayout;
import com.codename1.ui.layouts.LayeredLayout;
import com.codename1.ui.plaf.Style;
import com.codename1.ui.util.Resources;
import com.mycompany.entities.Cours;
import com.mycompany.entities.Reclamation;
import com.mycompany.entities.SessionManager;
import com.mycompany.services.ServiceCours;

/**
 * The user profile form
 *
 * @author Shai Almog
 */
public class UpdateCoursFormBack extends BaseFormBack {

    private static String i;

    public UpdateCoursFormBack(Resources res,  float id,String nom,String description,String nomcoach,String nombre,String jour) {
        super("UpdateCoursFormBack", BoxLayout.y());

        Toolbar tb = new Toolbar(true);
        setToolbar(tb);
        getTitleArea().setUIID("Container");
        setTitle("Profile");
        getContentPane().setScrollVisible(false);
        Form previous = Display.getInstance().getCurrent();
        tb.setBackCommand("", e -> previous.showBack());
        super.addSideMenu(res);

        Image img = res.getImage("profile-background.jpg");
        if (img.getHeight() > Display.getInstance().getDisplayHeight() / 3) {
            img = img.scaledHeight(Display.getInstance().getDisplayHeight() / 3);
        }
        ScaleImageLabel sl = new ScaleImageLabel(img);
        sl.setUIID("BottomPad");
        sl.setBackgroundType(Style.BACKGROUND_IMAGE_SCALED_FILL);

        Button btnValider = new Button("Valider");
//Label pp= new Label(ServiceUser.UriImage(SessionManager.getPhoto()),"PictureWhiteBackground");
        add(LayeredLayout.encloseIn(sl, BorderLayout.south(GridLayout.encloseIn(3, FlowLayout.encloseCenter()))));

        TextField titree = new TextField(nom);
        titree.setUIID("TextFieldBlack");
        addStringValue("nom", titree);
        TextField contenuu = new TextField(description);
        contenuu.setUIID("TextFieldBlack");
        addStringValue("contenu", contenuu);
        TextField nomcoache = new TextField(nomcoach);
        nomcoache.setUIID("TextFieldBlack");
        addStringValue("nomcoache", nomcoache);
        TextField nombree = new TextField(nombre);
        nombree.setUIID("TextFieldBlack");
        addStringValue("nombree", nombree);
        TextField joure = new TextField(jour);
        joure.setUIID("TextFieldBlack");
        addStringValue("joure", joure);
        Button del = new Button("Delete");
        del.setUIID("Delete");
        addStringValue("", del);
        btnValider.setUIID("Valider");
        addStringValue("", btnValider);
        TextField path = new TextField("");

        btnValider.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent evt) {
                if ((titree.getText().length() == 0) || (contenuu.getText().length() == 0)) {
                    Dialog.show("Alert", "Please fill all the fields", new Command("OK"));
                } else {
                    try {
                        System.out.println(id);
                        Cours t = new Cours(id, titree.getText(), contenuu.getText(),nomcoache.getText(),nombree.getText(),joure.getText());
                        if (ServiceCours.getInstance().updateCours(t)) {
                            Dialog.show("Success", "Connection accepted", new Command("OK"));
                        } else {
                            Dialog.show("ERROR", "Server error", new Command("OK"));
                        }
                    } catch (NumberFormatException e) {
                        Dialog.show("ERROR", "Status must be a number", new Command("OK"));
                    }

                }

            }
        });
        del.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent evt) {
                ServiceCours.getInstance().deletCours(id);
                Dialog.show("Event Deleted", "OK");
            }
        });

    }

    private void addStringValue(String s, Component v) {
        add(BorderLayout.west(new Label(s, "PaddedLabel")).
                add(BorderLayout.CENTER, v));
        add(createLineSeparator(0xeeeeee));
    }
}